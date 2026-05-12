"""
MySQL JSON API for Flask dev server (port 8080): same contract as android-app/.../api/*.php
so signup/login work when PHP is not executed (Flask serves .php as static HTML only).

Uses env vars matching db.php: BEANTHENTIC_DB_HOST, BEANTHENTIC_DB_PORT, BEANTHENTIC_DB_NAME,
BEANTHENTIC_DB_USER, BEANTHENTIC_DB_PASS.
"""
from __future__ import annotations

import os
import re
import math
from datetime import datetime
from typing import Dict

import bcrypt
import pymysql
from pymysql.cursors import DictCursor
from pymysql.err import IntegrityError
from flask import Response, jsonify, request


def _db_params() -> dict:
    return {
        "host": os.environ.get("BEANTHENTIC_DB_HOST", "127.0.0.1"),
        "port": int(os.environ.get("BEANTHENTIC_DB_PORT", "3306")),
        "user": os.environ.get("BEANTHENTIC_DB_USER", "root"),
        "password": os.environ.get("BEANTHENTIC_DB_PASS", ""),
        "database": os.environ.get("BEANTHENTIC_DB_NAME", "beanthentic_app"),
        "charset": "utf8mb4",
        "cursorclass": DictCursor,
        "autocommit": False,
    }


def _connect():
    return pymysql.connect(**_db_params())


def normalize_phone(raw: str) -> str:
    s = (raw or "").strip()
    if not s or "@" in s:
        return ""
    digits = re.sub(r"\D+", "", s)
    if not digits:
        return ""
    if digits[0] == "0":
        digits = digits[1:]
    if len(digits) >= 2 and digits[:2] == "63":
        digits = digits[2:]
    if len(digits) == 10 and digits[0] == "9":
        return "+63" + digits
    return s


def parse_login_identifier(raw: str) -> Dict[str, str]:
    t = (raw or "").strip()
    if not t:
        return {"type": "empty", "email": "", "phone": ""}
    if "@" in t:
        return {"type": "email", "email": t.lower(), "phone": ""}
    ph = normalize_phone(t)
    if ph and ph.startswith("+63"):
        return {"type": "phone", "email": "", "phone": ph}
    return {"type": "phone", "email": "", "phone": ph}


def _json_response(data: dict, status: int = 200) -> Response:
    r = jsonify(data)
    r.status_code = status
    r.headers["Content-Type"] = "application/json; charset=utf-8"
    return r


def handle_signup() -> Response:
    if request.method != "POST":
        return _json_response({"ok": False, "error": "Method not allowed"}, 405)
    body = request.get_json(silent=True) or {}
    raw_phone = str(body.get("phone_number") or "").strip()
    password = str(body.get("password") or "")
    name = str(body.get("name") or body.get("full_name") or "").strip()
    email_in = str(body.get("email") or "").strip()
    email = None
    if email_in:
        if re.match(r"^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$", email_in):
            email = email_in.lower()

    phone = normalize_phone(raw_phone)
    if not phone or not phone.startswith("+63"):
        return _json_response({"ok": False, "error": "Enter a valid Philippine mobile number (9XXXXXXXXX)."}, 400)
    if len(password) < 8:
        return _json_response({"ok": False, "error": "Password must be at least 8 characters."}, 400)

    display = name if name else None
    pw_hash = bcrypt.hashpw(password.encode("utf-8"), bcrypt.gensalt()).decode("utf-8")

    first = last = None
    if display:
        parts = display.split()
        first = (parts[0] or "").strip() or None
        last = (parts[-1] or "").strip() or None if len(parts) > 1 else None
        if len(parts) == 1:
            last = None
        if first == last and first:
            last = None

    conn = None
    try:
        conn = _connect()
        with conn.cursor() as cur:
            cur.execute("SELECT user_id FROM users WHERE phone_number = %s LIMIT 1", (phone,))
            if cur.fetchone():
                return _json_response({"ok": False, "error": "Phone number already registered."}, 409)
            if email:
                cur.execute(
                    "SELECT user_id FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(%s)) LIMIT 1",
                    (email,),
                )
                if cur.fetchone():
                    return _json_response({"ok": False, "error": "Email already registered."}, 409)

            cur.execute(
                "INSERT INTO users (phone_number, email, username, password_hash, role) VALUES (%s, %s, %s, %s, %s)",
                (phone, email, display, pw_hash, "farmer"),
            )
            user_id = cur.lastrowid
            cur.execute('INSERT INTO farmers (user_id, status) VALUES (%s, "pending")', (user_id,))
            farmer_id = cur.lastrowid
            cur.execute(
                "INSERT INTO personal_information (farmer_id, first_name, last_name, contact_number) VALUES (%s, %s, %s, %s)",
                (farmer_id, first, last, phone),
            )
            cur.execute("INSERT INTO account_settings (user_id) VALUES (%s)", (user_id,))
        conn.commit()
        return _json_response(
            {
                "ok": True,
                "user": {
                    "user_id": int(user_id),
                    "farmer_id": int(farmer_id),
                    "phone_number": phone,
                    "email": email,
                    "name": display or "",
                },
            },
            200,
        )
    except IntegrityError as e:
        if conn:
            conn.rollback()
        return _json_response({"ok": False, "error": "Phone number or email already registered."}, 409)
    except Exception as e:
        if conn:
            conn.rollback()
        return _json_response({"ok": False, "error": f"Signup failed: {e!s}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_login() -> Response:
    if request.method != "POST":
        return _json_response({"ok": False, "error": "Method not allowed"}, 405)
    body = request.get_json(silent=True) or {}
    raw_login = str(body.get("phone_number") or body.get("login") or "").strip()
    password = str(body.get("password") or "")
    if not raw_login or not password:
        return _json_response({"ok": False, "error": "Phone (or email) and password are required."}, 400)

    parsed = parse_login_identifier(raw_login)
    if parsed["type"] == "empty":
        return _json_response({"ok": False, "error": "Enter a valid phone number or email."}, 400)

    conn = None
    try:
        conn = _connect()
        with conn.cursor() as cur:
            if parsed["type"] == "email" and parsed["email"]:
                cur.execute(
                    """
                    SELECT u.user_id, u.phone_number, u.password_hash, u.username, u.email, f.farmer_id,
                           pi.first_name, pi.last_name
                    FROM users u
                    LEFT JOIN farmers f ON f.user_id = u.user_id
                    LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
                    WHERE LOWER(TRIM(u.email)) = LOWER(TRIM(%s)) AND u.is_active = 1
                    LIMIT 1
                    """,
                    (parsed["email"],),
                )
            else:
                ph = parsed["phone"]
                if not ph:
                    return _json_response({"ok": False, "error": "Enter a valid Philippine mobile number."}, 400)
                cur.execute(
                    """
                    SELECT u.user_id, u.phone_number, u.password_hash, u.username, u.email, f.farmer_id,
                           pi.first_name, pi.last_name
                    FROM users u
                    LEFT JOIN farmers f ON f.user_id = u.user_id
                    LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
                    WHERE u.phone_number = %s AND u.is_active = 1
                    LIMIT 1
                    """,
                    (ph,),
                )
            row = cur.fetchone()
        if not row:
            return _json_response({"ok": False, "error": "Account not found."}, 404)
        stored = (row.get("password_hash") or "").encode("utf-8")
        if not bcrypt.checkpw(password.encode("utf-8"), stored):
            return _json_response({"ok": False, "error": "Invalid password."}, 401)

        name = (row.get("username") or "").strip()
        if not name:
            fn = (row.get("first_name") or "").strip()
            ln = (row.get("last_name") or "").strip()
            name = (fn + " " + ln).strip()
        if not name:
            name = str(row.get("phone_number") or raw_login)

        with conn.cursor() as cur:
            cur.execute("UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE user_id = %s", (int(row["user_id"]),))
        conn.commit()

        fid = row.get("farmer_id")
        return _json_response(
            {
                "ok": True,
                "user": {
                    "user_id": int(row["user_id"]),
                    "farmer_id": int(fid) if fid is not None else None,
                    "phone_number": str(row.get("phone_number") or ""),
                    "email": row.get("email"),
                    "name": name,
                },
            },
            200,
        )
    except Exception as e:
        if conn:
            conn.rollback()
        return _json_response({"ok": False, "error": f"Login failed: {e!s}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_session_verify() -> Response:
    if request.method != "POST":
        return _json_response({"ok": False, "reason": "method"}, 405)
    body = request.get_json(silent=True) or {}
    user_id = int(body.get("user_id") or 0)
    login_key = str(body.get("email") or "").strip()
    if user_id <= 0 or not login_key:
        return _json_response({"ok": False, "reason": "bad_request"}, 200)

    parsed = parse_login_identifier(login_key)
    conn = None
    try:
        conn = _connect()
        with conn.cursor() as cur:
            if parsed["type"] == "email" and parsed["email"]:
                cur.execute(
                    """
                    SELECT 1 FROM users
                    WHERE user_id = %s AND LOWER(TRIM(COALESCE(email, ''))) = %s AND is_active = 1
                    LIMIT 1
                    """,
                    (user_id, parsed["email"].lower()),
                )
            elif parsed["type"] == "phone" and parsed.get("phone"):
                cur.execute(
                    "SELECT 1 FROM users WHERE user_id = %s AND phone_number = %s AND is_active = 1 LIMIT 1",
                    (user_id, parsed["phone"]),
                )
            else:
                return _json_response({"ok": False, "reason": "identifier"}, 200)
            ok = cur.fetchone() is not None
        return _json_response({"ok": ok}, 200)
    except Exception as e:
        return _json_response({"ok": False, "reason": "db", "error": str(e)}, 503)
    finally:
        if conn:
            conn.close()


LIPA_BARANGAYS = [
    "Adya",
    "Antipolo del Sur",
    "Bagong Pook",
    "Bulacnin",
    "Halang",
    "Kayumanggi",
    "Latag",
    "Lodlod",
    "Lumbang",
    "Malagonlong",
    "Malitlit",
    "Pagolingin",
    "Pangao",
    "Pinagkawitan",
    "Pinagtong-Ulan",
    "Pusil",
    "Quezon",
    "Rizal",
    "San Benito",
    "San Celestino",
    "San Isidro",
    "San Salvador",
    "Santo Niño",
    "Santo Toribio",
    "Talisay",
    "Tangob",
    "Tangway",
    "Tipakan",
]


def _fr_qty_to_kg(qty, unit: str) -> float:
    try:
        q = float(qty)
    except (TypeError, ValueError):
        q = 0.0
    if q <= 0:
        return 0.0
    u = (unit or "").strip().upper()
    if u == "G":
        return q / 1000.0
    if u == "LB":
        return q * 0.45359237
    return q


def _fr_map_ownership_wizard(raw: str) -> str:
    s = (raw or "").strip().lower()
    m = {
        "landowner": "owner",
        "cloa_holder": "owner",
        "list_holder": "other",
        "sessional_farm_worker": "tenant",
        "others": "other",
        "owned": "owner",
        "owner": "owner",
        "tenant": "tenant",
        "lessee": "tenant",
        "co-owner": "co-owner",
        "co_owner": "co-owner",
        "coowner": "co-owner",
        "usufruct": "other",
        "other": "other",
    }
    return m.get(s, "other")


def _fr_rsbsa_tiny(raw: str) -> int:
    return 1 if (raw or "").strip().lower() == "yes" else 0


def validate_farmer_payload_py(d: dict, user_id: int) -> dict:
    err: dict[str, str] = {}
    if user_id <= 0:
        err["user_id"] = "Missing account. Log in again via XAMPP."
    first = str(d.get("first_name") or "").strip()
    last = str(d.get("last_name") or "").strip()
    if len(first) < 2:
        err["first_name"] = "Enter your first name."
    if len(last) < 2:
        err["last_name"] = "Enter your last name."
    barangay = str(d.get("barangay") or "").strip()
    if not barangay:
        err["barangay"] = "Select your barangay in Lipa City."
    elif barangay not in LIPA_BARANGAYS:
        err["barangay"] = "Barangay must be within Lipa City."

    role = str(d.get("affiliation_role") or "").strip()
    if not role:
        err["affiliation_role"] = "Select your role."
    ncfrs = str(d.get("ncfrs") or "").strip().lower()
    if ncfrs not in ("yes", "no"):
        err["ncfrs"] = "Select NCFRS (Yes or No)."
    rsb = str(d.get("rsbsa_registered") or "").strip().lower()
    if rsb not in ("yes", "no", "pending"):
        err["rsbsa_registered"] = "Select RSBSA registration status."
    rsb_num = str(d.get("rsbsa_number") or "").strip()
    if rsb == "yes" and len(rsb_num) < 4:
        err["rsbsa_number"] = "Enter your RSBSA number."

    own = str(d.get("ownership_status") or "").strip().lower()
    if own not in ("landowner", "cloa_holder", "list_holder", "sessional_farm_worker", "others"):
        err["ownership_status"] = "Select status of ownership."
    unit = str(d.get("plant_area_unit") or "").strip().lower()
    if unit not in ("ha", "sqm", "ac"):
        err["plant_area_unit"] = "Select a unit."
    raw_a = str(d.get("plant_area_value", "")).strip()
    if raw_a == "":
        err["plant_area_value"] = "Enter total plant area."
    else:
        try:
            a = float(raw_a)
            if not math.isfinite(a) or a <= 0:
                err["plant_area_value"] = "Enter an area greater than zero."
            elif a > 1e6:
                err["plant_area_value"] = "Value is too large."
        except (TypeError, ValueError):
            err["plant_area_value"] = "Enter total plant area."

    for v in ("liberica", "robusta", "excelsa"):
        for suffix in ("_bearing", "_non_bearing"):
            key = v + suffix
            raw = str(d.get(key, "")).strip()
            if raw == "":
                continue
            try:
                n = int(raw)
                if str(n) != raw or n < 0:
                    err[key] = "Use a whole number ≥ 0."
            except ValueError:
                err[key] = "Use a whole number ≥ 0."

    photo = str(d.get("profile_photo_data") or "").strip()
    if not photo:
        err["profile_photo_data"] = "Please take or upload a profile photo."

    if str(d.get("agree_registration") or "") != "yes":
        err["agree_registration"] = "Please confirm the declaration before submitting."

    return err


def farmer_mysql_save_py(conn, user_id: int, body: dict) -> int:
    cur = conn.cursor()
    cur.execute("SELECT user_id FROM users WHERE user_id = %s AND is_active = 1 LIMIT 1", (user_id,))
    if not cur.fetchone():
        raise RuntimeError("Invalid user.")

    first = str(body.get("first_name") or "").strip()
    last = str(body.get("last_name") or "").strip()
    phone = normalize_phone(str(body.get("phone") or "").strip())
    phone_sql = phone if phone else None
    barangay = str(body.get("barangay") or "").strip()
    farm_addr = str(body.get("farm_address") or "").strip()
    ownership = _fr_map_ownership_wizard(str(body.get("ownership_status") or ""))
    plant_val = body.get("plant_area_value")
    plant_unit = str(body.get("plant_area_unit") or "").strip().lower()
    plant_ha = None
    if plant_val is not None and str(plant_val).strip() != "":
        try:
            pv = float(plant_val)
            if plant_unit == "ha":
                plant_ha = pv
            elif plant_unit == "sqm":
                plant_ha = pv / 10000.0
            elif plant_unit == "ac":
                plant_ha = pv * 0.40468564224
        except (TypeError, ValueError):
            plant_ha = None

    try:
        year = int(body.get("production_year") or 0)
    except (TypeError, ValueError):
        year = 0
    if year < 2000 or year > 2100:
        year = datetime.now().year

    lib_kg = _fr_qty_to_kg(body.get("liberica_prod_qty") or 0, str(body.get("liberica_prod_unit") or "kg"))
    rob_kg = _fr_qty_to_kg(body.get("robusta_prod_qty") or 0, str(body.get("robusta_prod_unit") or "kg"))
    exc_kg = _fr_qty_to_kg(body.get("excelsa_prod_qty") or 0, str(body.get("excelsa_prod_unit") or "kg"))

    fed = str(body.get("federation") or body.get("affiliation_role") or "").strip()
    assoc = str(body.get("association") or "").strip()
    rsb = str(body.get("rsbsa_registered") or "")
    rsb_no = str(body.get("rsbsa_number") or "").strip()

    try:
        conn.begin()
        cur.execute("SELECT farmer_id FROM farmers WHERE user_id = %s LIMIT 1", (user_id,))
        row = cur.fetchone()
        if row:
            farmer_id = int(row["farmer_id"])
            cur.execute('UPDATE farmers SET status = "active" WHERE farmer_id = %s', (farmer_id,))
        else:
            cur.execute('INSERT INTO farmers (user_id, status) VALUES (%s, "active")', (user_id,))
            farmer_id = int(cur.lastrowid)

        addr_line = farm_addr if farm_addr else (barangay if barangay else None)

        cur.execute("SELECT personal_info_id FROM personal_information WHERE farmer_id = %s LIMIT 1", (farmer_id,))
        if cur.fetchone():
            cur.execute(
                """UPDATE personal_information SET first_name = %s, last_name = %s,
                   contact_number = COALESCE(%s, contact_number), barangay = %s,
                   current_address = COALESCE(%s, current_address) WHERE farmer_id = %s""",
                (first or None, last or None, phone_sql, barangay or None, addr_line, farmer_id),
            )
        else:
            cur.execute(
                """INSERT INTO personal_information (farmer_id, first_name, last_name, contact_number, barangay, current_address)
                   VALUES (%s, %s, %s, %s, %s, %s)""",
                (farmer_id, first or None, last or None, phone_sql, barangay or None, addr_line),
            )

        cur.execute("SELECT farm_info_id FROM farm_information WHERE farmer_id = %s LIMIT 1", (farmer_id,))
        if cur.fetchone():
            cur.execute(
                """UPDATE farm_information SET ownership_status = %s, farm_address = COALESCE(%s, farm_address),
                   barangay = COALESCE(%s, barangay), farm_size_ha = COALESCE(%s, farm_size_ha) WHERE farmer_id = %s""",
                (ownership, addr_line, barangay or None, plant_ha, farmer_id),
            )
        else:
            cur.execute(
                """INSERT INTO farm_information (farmer_id, ownership_status, farm_address, barangay, farm_size_ha)
                   VALUES (%s, %s, %s, %s, %s)""",
                (farmer_id, ownership, addr_line, barangay or None, plant_ha),
            )

        cur.execute("SELECT affiliation_info_id FROM affiliation_information WHERE farmer_id = %s LIMIT 1", (farmer_id,))
        if cur.fetchone():
            cur.execute(
                """UPDATE affiliation_information SET federation_assoc = %s, coop_name = %s,
                   rsbsa_registered = %s, rsbsa_number = %s WHERE farmer_id = %s""",
                (fed or None, assoc or None, _fr_rsbsa_tiny(rsb), rsb_no or None, farmer_id),
            )
        else:
            cur.execute(
                """INSERT INTO affiliation_information (farmer_id, federation_assoc, coop_name, rsbsa_registered, rsbsa_number)
                   VALUES (%s, %s, %s, %s, %s)""",
                (farmer_id, fed or None, assoc or None, _fr_rsbsa_tiny(rsb), rsb_no or None),
            )

        rb = int(body.get("robusta_bearing") or 0)
        rn = int(body.get("robusta_non_bearing") or 0)
        lb = int(body.get("liberica_bearing") or 0)
        ln = int(body.get("liberica_non_bearing") or 0)
        eb = int(body.get("excelsa_bearing") or 0)
        en = int(body.get("excelsa_non_bearing") or 0)

        cur.execute(
            "SELECT tree_count_id FROM tree_counts WHERE farmer_id = %s AND record_year = %s LIMIT 1",
            (farmer_id, year),
        )
        if cur.fetchone():
            cur.execute(
                """UPDATE tree_counts SET robusta_bearing = %s, robusta_non_bearing = %s, liberica_bearing = %s,
                   liberica_non_bearing = %s, excelsa_bearing = %s, excelsa_non_bearing = %s
                   WHERE farmer_id = %s AND record_year = %s""",
                (rb, rn, lb, ln, eb, en, farmer_id, year),
            )
        else:
            cur.execute(
                """INSERT INTO tree_counts (farmer_id, record_year, robusta_bearing, robusta_non_bearing,
                   liberica_bearing, liberica_non_bearing, excelsa_bearing, excelsa_non_bearing)
                   VALUES (%s, %s, %s, %s, %s, %s, %s, %s)""",
                (farmer_id, year, rb, rn, lb, ln, eb, en),
            )

        total_kg = lib_kg + rob_kg + exc_kg
        cur.execute(
            "SELECT production_info_id FROM production_information WHERE farmer_id = %s AND production_year = %s LIMIT 1",
            (farmer_id, year),
        )
        if cur.fetchone():
            cur.execute(
                """UPDATE production_information SET robusta_qty_kg = %s, liberica_qty_kg = %s, excelsa_qty_kg = %s,
                   beans_remaining_kg = %s WHERE farmer_id = %s AND production_year = %s""",
                (rob_kg, lib_kg, exc_kg, total_kg, farmer_id, year),
            )
        else:
            cur.execute(
                """INSERT INTO production_information (farmer_id, production_year, robusta_qty_kg, liberica_qty_kg,
                   excelsa_qty_kg, beans_remaining_kg) VALUES (%s, %s, %s, %s, %s, %s)""",
                (farmer_id, year, rob_kg, lib_kg, exc_kg, total_kg),
            )

        conn.commit()
        return farmer_id
    except Exception:
        conn.rollback()
        raise


def handle_register_farm_farmer() -> Response:
    if request.method != "POST":
        r = jsonify({"success": False, "errors": {"_error": "Method not allowed"}})
        r.status_code = 405
        return r
    body = request.get_json(silent=True) or {}
    try:
        user_id = int(body.get("user_id") or 0)
    except (TypeError, ValueError):
        user_id = 0
    errors = validate_farmer_payload_py(body, user_id)
    if errors:
        r = jsonify({"success": False, "errors": errors})
        r.status_code = 400
        return r
    conn = None
    try:
        conn = _connect()
        farmer_id = farmer_mysql_save_py(conn, user_id, body)
        r = jsonify(
            {
                "success": True,
                "farmer_id": farmer_id,
                "message": "Farmer registered successfully",
            }
        )
        r.status_code = 200
        return r
    except RuntimeError as e:
        r = jsonify({"success": False, "errors": {"_error": str(e)}})
        r.status_code = 400
        return r
    except Exception as e:
        r = jsonify({"success": False, "errors": {"_error": str(e)}})
        r.status_code = 500
        return r
    finally:
        if conn:
            conn.close()


def register_mysql_json_routes(app) -> None:
    """Register /api/*.php routes that mirror PHP when Flask is the HTTP server."""

    @app.route("/api/signup.php", methods=["POST"])
    def _signup():
        return handle_signup()

    @app.route("/api/login.php", methods=["POST"])
    def _login():
        return handle_login()

    @app.route("/api/session_verify.php", methods=["POST"])
    def _session_verify():
        return handle_session_verify()

    @app.route("/api/register_farm_farmer.php", methods=["POST"])
    def _register_farm_farmer():
        return handle_register_farm_farmer()
