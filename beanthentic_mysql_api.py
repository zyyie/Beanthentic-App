"""
MySQL JSON API for Flask dev server (port 8080): same contract as android-app/.../api/*.php
so signup/login work when PHP is not executed (Flask serves .php as static HTML only).

Uses env vars matching db.php: BEANTHENTIC_DB_HOST, BEANTHENTIC_DB_PORT, BEANTHENTIC_DB_NAME,
BEANTHENTIC_DB_USER, BEANTHENTIC_DB_PASS.
"""
from __future__ import annotations

import base64
import hashlib
import json
import os
import re
import math
import sys
from datetime import date, datetime
from typing import Any, Dict, Optional

import bcrypt
import pymysql
from pymysql.cursors import DictCursor
from pymysql.err import IntegrityError
from flask import Response, jsonify, request

from beanthentic_gi_updates_api import handle_admin_gi_contributions, handle_gi_updates


def _db_params() -> dict:
    # Default: XAMPP on same PC (127.0.0.1, root, no password). Override via env vars.
    default_host = "127.0.0.1"
    return {
        "host": os.environ.get("BEANTHENTIC_DB_HOST", default_host),
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

def _ownership_columns(raw) -> dict[str, str]:
    """Map ownership_status to farm-table X columns (Landowner, CLOA, Lease, Seasonal, Others)."""
    s = str(raw or "").strip().lower()
    cols = {k: "" for k in ("LANDOWNER", "CLOA", "LEASE", "SEASONAL", "OTHERS")}

    def mark(key: str) -> None:
        cols[key] = "X"

    wizard = {
        "landowner": "LANDOWNER",
        "cloa_holder": "CLOA",
        "cloa holder": "CLOA",
        "list_holder": "LEASE",
        "list holder": "LEASE",
        "sessional_farm_worker": "SEASONAL",
        "sessional farm worker": "SEASONAL",
        "others": "OTHERS",
    }
    if s in wizard:
        mark(wizard[s])
    elif s in {"a": "LANDOWNER", "b": "CLOA", "c": "LEASE", "d": "SEASONAL", "e": "OTHERS"}:
        mark({"a": "LANDOWNER", "b": "CLOA", "c": "LEASE", "d": "SEASONAL", "e": "OTHERS"}[s])
    elif s in {
        "owner": "LANDOWNER",
        "owned": "LANDOWNER",
        "tenant": "SEASONAL",
        "lessee": "LEASE",
        "co-owner": "CLOA",
        "co_owner": "CLOA",
        "coowner": "CLOA",
        "other": "OTHERS",
        "usufruct": "OTHERS",
    }:
        mark(
            {
                "owner": "LANDOWNER",
                "owned": "LANDOWNER",
                "tenant": "SEASONAL",
                "lessee": "LEASE",
                "co-owner": "CLOA",
                "co_owner": "CLOA",
                "coowner": "CLOA",
                "other": "OTHERS",
                "usufruct": "OTHERS",
            }[s]
        )
    elif "landowner" in s:
        mark("LANDOWNER")
    elif "cloa" in s:
        mark("CLOA")
    elif "lease" in s or "list" in s or "lessee" in s:
        mark("LEASE")
    elif "seasonal" in s or "sessional" in s:
        mark("SEASONAL")
    elif s:
        mark("OTHERS")

    return {
        **cols,
        "OWNER_OPERATOR": cols["LANDOWNER"],
        "LESSOR": cols["CLOA"],
        "LESSEE": cols["LEASE"],
        "SHAREHOLDER": cols["SEASONAL"],
    }


_BASE_DIR = os.path.dirname(os.path.abspath(__file__))
_ASSETS_DIR = os.path.join(_BASE_DIR, "android-app", "app", "src", "main", "assets")
_FARMER_UPLOADS_DIR = os.path.join(_ASSETS_DIR, "uploads", "farmers")
_CLIENT_ID_UPLOADS_DIR = os.path.join(_ASSETS_DIR, "uploads", "client_ids")


def _mysql_save_profile_photo_file(farmer_id: int, photo_data: str) -> Optional[str]:
    """Save data-URL image to assets/uploads/farmers/; return path like /uploads/farmers/farmer_12.jpg."""
    if farmer_id <= 0:
        return None
    raw = (photo_data or "").strip()
    m = re.match(r"^data:image/(jpeg|jpg|png|webp);base64,", raw, re.I)
    if not m:
        return None
    comma = raw.find(",")
    if comma < 0:
        return None
    try:
        blob = base64.b64decode(raw[comma + 1 :], validate=True)
    except Exception:
        return None
    if len(blob) < 64:
        return None
    ext = m.group(1).lower()
    if ext == "jpeg":
        ext = "jpg"
    if ext not in ("jpg", "png", "webp"):
        ext = "jpg"
    os.makedirs(_FARMER_UPLOADS_DIR, exist_ok=True)
    rel = f"/uploads/farmers/farmer_{farmer_id}.{ext}"
    path = os.path.join(_ASSETS_DIR, "uploads", "farmers", f"farmer_{farmer_id}.{ext}")
    with open(path, "wb") as f:
        f.write(blob)
    return rel


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


def _format_mysql_datetime(value) -> str:
    if value is None:
        return ""
    if hasattr(value, "strftime"):
        return value.strftime("%Y-%m-%d %H:%M:%S")
    s = str(value).strip()
    m = re.match(r"^(\d{4}-\d{2}-\d{2})[ T](\d{2}:\d{2}:\d{2})", s)
    if m:
        return f"{m.group(1)} {m.group(2)[:8]}"
    return s


def _parse_client_created_at(raw: str) -> str | None:
    """Device/browser local wall clock for MySQL (YYYY-MM-DD HH:mm:ss)."""
    s = (raw or "").strip()
    if not s:
        return None
    m = re.match(r"^(\d{4}-\d{2}-\d{2})[ T](\d{2}:\d{2}(?::\d{2})?)$", s)
    if m:
        t = m.group(2)
        if len(t) == 5:
            t = t + ":00"
        return f"{m.group(1)} {t[:8]}"
    if "T" in s:
        try:
            from zoneinfo import ZoneInfo

            iso = s.replace("Z", "+00:00")
            dt = datetime.fromisoformat(iso)
            if dt.tzinfo is None:
                return dt.strftime("%Y-%m-%d %H:%M:%S")
            ph = dt.astimezone(ZoneInfo("Asia/Manila"))
            return ph.strftime("%Y-%m-%d %H:%M:%S")
        except Exception:
            return None
    return None


def farmer_display_name_from_row(row: dict) -> str:
    """Full name from personal_information, else username, else Farmer #id."""
    first = str(row.get("first_name") or "").strip()
    last = str(row.get("last_name") or "").strip()
    full = f"{first} {last}".strip()
    if full:
        return full
    username = str(row.get("username") or "").strip()
    if username:
        return username
    farmer_id = row.get("farmer_id")
    if farmer_id:
        return f"Farmer #{farmer_id}"
    return "Farmer"


_FARMER_USER_LOOKUP_SQL = """
    SELECT u.phone_number, u.username, f.farmer_id,
           COALESCE(NULLIF(TRIM(pi.first_name), ''), '') AS first_name,
           COALESCE(NULLIF(TRIM(pi.last_name), ''), '') AS last_name
    FROM users u
    LEFT JOIN farmers f ON f.user_id = u.user_id
    LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
    WHERE u.user_id = %s
    LIMIT 1
"""


def phone_variants(raw: str) -> list[str]:
    """All common PH formats for one number (+63, 09…, 639…)."""
    out: list[str] = []
    seen: set[str] = set()

    def add(val: str) -> None:
        v = (val or "").strip()
        if not v or v in seen:
            return
        seen.add(v)
        out.append(v)

    add(normalize_phone(raw))
    digits = re.sub(r"\D+", "", raw or "")
    if not digits:
        return out
    add(digits)
    if digits.startswith("0") and len(digits) >= 11:
        add("+63" + digits[1:])
        add(digits[1:])
    if digits.startswith("63") and len(digits) >= 12:
        add("+63" + digits[2:])
        add("0" + digits[2:])
        add(digits[2:])
    if len(digits) == 10 and digits.startswith("9"):
        add("+63" + digits)
        add("0" + digits)
        add("63" + digits)
    return out


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
    _cors_headers(r)
    return r


def _cors_headers(resp: Response) -> Response:
    """
    Allow calling this API from LAN clients and from 192.168 page -> 127.0.0.1 dev proxy.
    """
    origin = request.headers.get("Origin")
    resp.headers["Access-Control-Allow-Origin"] = origin or "*"
    resp.headers["Vary"] = "Origin"
    resp.headers["Access-Control-Allow-Methods"] = "GET, POST, OPTIONS"
    resp.headers["Access-Control-Allow-Headers"] = "Content-Type, Authorization"
    resp.headers["Access-Control-Max-Age"] = "86400"
    return resp


def _preflight_ok() -> Response:
    r = Response("", status=204)
    return _cors_headers(r)


def _ensure_shared_messages_table(conn) -> None:
    with conn.cursor() as cur:
        cur.execute(
            """
            CREATE TABLE IF NOT EXISTS shared_messages (
              message_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              sender_role ENUM('admin','farmer') NOT NULL,
              sender_phone VARCHAR(32) NOT NULL,
              sender_name VARCHAR(255) NULL,
              recipient_role ENUM('admin','farmer') NOT NULL,
              recipient_phone VARCHAR(32) NOT NULL DEFAULT '',
              recipient_name VARCHAR(255) NULL,
              subject VARCHAR(300) NOT NULL,
              body TEXT NOT NULL,
              category VARCHAR(30) NOT NULL DEFAULT 'general',
              farmer_id BIGINT UNSIGNED NULL,
              is_read TINYINT(1) NOT NULL DEFAULT 0,
              is_starred TINYINT(1) NOT NULL DEFAULT 0,
              is_archived TINYINT(1) NOT NULL DEFAULT 0,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              read_at DATETIME NULL,
              INDEX idx_sm_recipient (recipient_role, recipient_phone, is_read, is_archived),
              INDEX idx_sm_sender (sender_role, sender_phone),
              INDEX idx_sm_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            """
        )


def handle_chat_thread() -> Response:
    """
    Lightweight mobile chat API:
    - GET /api/chat_thread.php?user_id=<id>
    - POST /api/chat_thread.php { user_id, text }
    """
    if request.method == "OPTIONS":
        return _preflight_ok()

    conn = None
    try:
        conn = _connect()
        _ensure_shared_messages_table(conn)

        if request.method == "GET":
            try:
                user_id = int(request.args.get("user_id") or 0)
            except (TypeError, ValueError):
                user_id = 0
            if user_id <= 0:
                return _json_response({"ok": False, "error": "user_id is required"}, 400)

            with conn.cursor() as cur:
                cur.execute(_FARMER_USER_LOOKUP_SQL, (user_id,))
                user = cur.fetchone()
                if not user:
                    return _json_response({"ok": False, "error": "User not found"}, 404)
                phone = normalize_phone(str(user.get("phone_number") or "").strip())
                if not phone:
                    return _json_response({"ok": False, "error": "User phone not set"}, 400)
                display_name = farmer_display_name_from_row(user)

                variants = phone_variants(phone)
                ph = ", ".join(["%s"] * len(variants))
                cur.execute(
                    f"""
                    SELECT
                      message_id AS id,
                      sender_role,
                      sender_phone,
                      sender_name,
                      recipient_role,
                      recipient_phone,
                      recipient_name,
                      subject,
                      body,
                      category,
                      farmer_id,
                      is_read,
                      created_at
                    FROM shared_messages
                    WHERE
                      (sender_role='farmer' AND sender_phone IN ({ph}))
                      OR (recipient_role='farmer' AND recipient_phone IN ({ph}))
                    ORDER BY created_at ASC, message_id ASC
                    LIMIT 500
                    """,
                    tuple(variants) + tuple(variants),
                )
                items = cur.fetchall() or []
                for row in items:
                    if row.get("sender_role") is not None:
                        row["sender_role"] = str(row.get("sender_role"))
                    if row.get("sender_role") == "farmer":
                        row["sender_name"] = display_name
                    if row.get("created_at") is not None:
                        row["created_at"] = _format_mysql_datetime(row["created_at"])

                cur.execute(
                    f"""
                    UPDATE shared_messages
                    SET is_read = 1, read_at = NOW()
                    WHERE recipient_role='farmer'
                      AND recipient_phone IN ({ph})
                      AND is_read=0
                    """,
                    tuple(variants),
                )
                conn.commit()

            return _json_response({"ok": True, "phone": phone, "items": items}, 200)

        if request.method == "POST":
            body = request.get_json(silent=True) or {}
            try:
                user_id = int(body.get("user_id") or 0)
            except (TypeError, ValueError):
                user_id = 0
            text = str(body.get("text") or "").strip()
            if user_id <= 0 or not text:
                return _json_response({"ok": False, "error": "user_id and text are required"}, 400)

            with conn.cursor() as cur:
                cur.execute(_FARMER_USER_LOOKUP_SQL, (user_id,))
                user = cur.fetchone()
                if not user:
                    return _json_response({"ok": False, "error": "User not found"}, 404)

                phone = normalize_phone(str(user.get("phone_number") or "").strip())
                if not phone:
                    return _json_response({"ok": False, "error": "User phone not set"}, 400)
                name = farmer_display_name_from_row(user)
                farmer_id = user.get("farmer_id")
                created_at = _parse_client_created_at(str(body.get("client_created_at") or ""))
                if not created_at:
                    return _json_response(
                        {
                            "ok": False,
                            "error": "client_created_at is required (use device local time from the app).",
                        },
                        400,
                    )

                cur.execute(
                    """
                    INSERT INTO shared_messages
                      (sender_role, sender_phone, sender_name, recipient_role, recipient_phone,
                       recipient_name, subject, body, category, farmer_id, is_read, is_starred, is_archived, created_at)
                    VALUES
                      ('farmer', %s, %s, 'admin', '', 'Admin', 'Farmer Message', %s, 'farmers', %s, 0, 0, 0, %s)
                    """,
                    (
                        phone,
                        name[:255],
                        text,
                        int(farmer_id) if farmer_id else None,
                        created_at,
                    ),
                )
                mid = int(cur.lastrowid)
                conn.commit()

                new_row = {
                    "id": mid,
                    "sender_role": "farmer",
                    "sender_phone": phone,
                    "sender_name": name[:255],
                    "body": text,
                    "created_at": created_at,
                }

            return _json_response({"ok": True, "message_id": mid, "message": new_row, "phone": phone}, 200)

        return _json_response({"ok": False, "error": "Method not allowed"}, 405)
    except Exception as e:
        return _json_response({"ok": False, "error": f"chat_thread failed: {e!s}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_chat_unread_count() -> Response:
    """GET /api/chat_unread_count.php?user_id=N — unread admin messages for farmer badge."""
    if request.method == "OPTIONS":
        return _preflight_ok()
    if request.method != "GET":
        return _json_response({"ok": False, "error": "Method not allowed"}, 405)

    conn = None
    try:
        try:
            user_id = int(request.args.get("user_id") or 0)
        except (TypeError, ValueError):
            user_id = 0
        if user_id <= 0:
            return _json_response({"ok": False, "error": "user_id is required"}, 400)

        conn = _connect()
        _ensure_shared_messages_table(conn)
        with conn.cursor() as cur:
            cur.execute(_FARMER_USER_LOOKUP_SQL, (user_id,))
            user = cur.fetchone()
            if not user:
                return _json_response({"ok": False, "error": "User not found"}, 404)
            phone = normalize_phone(str(user.get("phone_number") or "").strip())
            if not phone:
                return _json_response({"ok": True, "unread_count": 0}, 200)
            variants = phone_variants(phone)
            ph = ", ".join(["%s"] * len(variants))
            cur.execute(
                f"""
                SELECT COUNT(*) AS c
                FROM shared_messages
                WHERE recipient_role='farmer'
                  AND recipient_phone IN ({ph})
                  AND sender_role='admin'
                  AND is_read=0
                  AND is_archived=0
                """,
                tuple(variants),
            )
            row = cur.fetchone() or {}
            count = int(row.get("c") or 0)
        return _json_response({"ok": True, "unread_count": count}, 200)
    except Exception as e:
        return _json_response({"ok": False, "error": f"chat_unread_count failed: {e!s}"}, 500)
    finally:
        if conn:
            conn.close()


def _fmt_birthday(value) -> str:
    if value is None or value == "":
        return ""
    if isinstance(value, datetime):
        return value.date().strftime("%Y-%m-%d")
    if isinstance(value, date):
        return value.strftime("%Y-%m-%d")
    raw = str(value).strip()
    if not raw:
        return ""
    m = re.match(r"^(\d{4}-\d{2}-\d{2})", raw)
    if m:
        return m.group(1)
    return raw


def _admin_farmer_data_items(conn) -> list[dict]:
    """
    Return farmer dataset for admin website over HTTP.
    This is used when remote MySQL is blocked from the admin device.
    """
    with conn.cursor() as cur:
        cur.execute(
            """
            SELECT
              f.farmer_id,
              u.username,
              u.phone_number,
              pi.first_name,
              pi.last_name,
              pi.birthday,
              COALESCE(pi.barangay, fi.barangay) AS barangay,
              fi.ownership_status,
              fi.farm_size_ha,
              ai.federation_assoc,
              ai.ncfrs,
              ai.rsbsa_registered,
              ai.rsbsa_number,
              ai.rsbsa_status,
              tc.robusta_bearing,
              tc.robusta_non_bearing,
              tc.liberica_bearing,
              tc.liberica_non_bearing,
              tc.excelsa_bearing,
              tc.excelsa_non_bearing,
              prod.robusta_qty_kg,
              prod.liberica_qty_kg,
              prod.excelsa_qty_kg
            FROM farmers f
            LEFT JOIN users u ON u.user_id = f.user_id
            LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
            LEFT JOIN farm_information fi ON fi.farmer_id = f.farmer_id
            LEFT JOIN affiliation_information ai ON ai.farmer_id = f.farmer_id
            LEFT JOIN tree_counts tc
              ON tc.farmer_id = f.farmer_id
             AND tc.record_year = (
                SELECT MAX(t2.record_year) FROM tree_counts t2 WHERE t2.farmer_id = f.farmer_id
              )
            LEFT JOIN production_information prod
              ON prod.farmer_id = f.farmer_id
             AND prod.production_year = (
                SELECT MAX(p2.production_year) FROM production_information p2 WHERE p2.farmer_id = f.farmer_id
              )
            ORDER BY f.farmer_id ASC
            LIMIT 2500
            """
        )
        rows = cur.fetchall() or []

    items: list[dict] = []
    for r in rows:
        first = str(r.get("first_name") or "").strip()
        last = str(r.get("last_name") or "").strip()
        display = str(r.get("username") or "").strip()
        if not display:
            display = (first + " " + last).strip()
        if not display:
            display = str(r.get("phone_number") or "").strip()
        if not display:
            display = f"Farmer #{int(r.get('farmer_id') or 0)}"

        rb = int(r.get("robusta_bearing") or 0)
        rn = int(r.get("robusta_non_bearing") or 0)
        lb = int(r.get("liberica_bearing") or 0)
        ln = int(r.get("liberica_non_bearing") or 0)
        eb = int(r.get("excelsa_bearing") or 0)
        en = int(r.get("excelsa_non_bearing") or 0)

        items.append(
            {
                "NO.": int(r.get("farmer_id") or 0),
                "NAME OF FARMER": display,
                "ADDRESS (BARANGAY)": str(r.get("barangay") or ""),
                "FA OFFICER / MEMBER": str(r.get("federation_assoc") or ""),
                "BIRTHDAY": _fmt_birthday(r.get("birthday")),
                "RSBSA Registered (Yes/No)": {
                    "yes": "Yes",
                    "pending": "Pending",
                    "no": "No",
                }[_rsbsa_label_from_db(r.get("rsbsa_registered"))],
                "RSBSA Registered Number": str(r.get("rsbsa_number") or ""),
                "RSBSA Status": _rsbsa_status_label(r.get("rsbsa_status") or ""),
                "STATUS OF OWNERSHIP": str(r.get("ownership_status") or ""),
                "Total Area Planted (HA.)": float(r.get("farm_size_ha") or 0) if r.get("farm_size_ha") is not None else 0,
                "LIBERICA BEARING": lb,
                "LIBERICA NON-BEARING": ln,
                "EXCELSA BEARING": eb,
                "EXCELSA NON-BEARING": en,
                "ROBUSTA BEARING": rb,
                "ROBUSTA NON-BEARING": rn,
                "TOTAL BEARING": lb + eb + rb,
                "TOTAL NON-BEARING": ln + en + rn,
                "TOTAL TREES": lb + eb + rb + ln + en + rn,
                "LIBERICA PRODUCTION": float(r.get("liberica_qty_kg") or 0) if r.get("liberica_qty_kg") is not None else 0,
                "EXCELSA PRODUCTION": float(r.get("excelsa_qty_kg") or 0) if r.get("excelsa_qty_kg") is not None else 0,
                "ROBUSTA PRODUCTION": float(r.get("robusta_qty_kg") or 0) if r.get("robusta_qty_kg") is not None else 0,
                "NCFRS": "Yes" if int(r.get("ncfrs") or 0) == 1 else "No",
                "REMARKS": "",
            }
        )

    return items


_CLIENT_FARMERS_SQL = """
            SELECT
              f.farmer_id,
              f.status,
              f.profile_photo,
              u.username,
              u.phone_number,
              u.email,
              pi.first_name,
              pi.last_name,
              COALESCE(pi.barangay, fi.barangay) AS barangay,
              ai.federation_assoc,
              ai.coop_name
            FROM farmers f
            INNER JOIN users u ON u.user_id = f.user_id
            LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
            LEFT JOIN farm_information fi ON fi.farmer_id = f.farmer_id
            LEFT JOIN affiliation_information ai ON ai.farmer_id = f.farmer_id
            ORDER BY COALESCE(f.updated_at, f.created_at) DESC, f.farmer_id DESC
            LIMIT 500
            """

_CLIENT_FARMERS_SQL_NO_COOP = """
            SELECT
              f.farmer_id,
              f.status,
              f.profile_photo,
              u.username,
              u.phone_number,
              u.email,
              pi.first_name,
              pi.last_name,
              COALESCE(pi.barangay, fi.barangay) AS barangay,
              ai.federation_assoc,
              '' AS coop_name
            FROM farmers f
            INNER JOIN users u ON u.user_id = f.user_id
            LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
            LEFT JOIN farm_information fi ON fi.farmer_id = f.farmer_id
            LEFT JOIN affiliation_information ai ON ai.farmer_id = f.farmer_id
            ORDER BY COALESCE(f.updated_at, f.created_at) DESC, f.farmer_id DESC
            LIMIT 500
            """


def _client_farmers_items(conn) -> list[dict]:
    """Farmers for Beanthentic-Client-Web (names, org, barangay, optional photo path)."""
    with conn.cursor() as cur:
        try:
            cur.execute(_CLIENT_FARMERS_SQL)
        except Exception as e:
            if "coop_name" not in str(e).lower():
                raise
            cur.execute(_CLIENT_FARMERS_SQL_NO_COOP)
        rows = cur.fetchall() or []

    items: list[dict] = []
    for r in rows:
        first = str(r.get("first_name") or "").strip()
        last = str(r.get("last_name") or "").strip()
        if not first and not last:
            full = str(r.get("username") or "").strip()
            if full:
                parts = full.split()
                if len(parts) >= 2:
                    last = parts[-1]
                    first = " ".join(parts[:-1])
                else:
                    first = full

        coop = str(r.get("coop_name") or "").strip()
        fed = str(r.get("federation_assoc") or "").strip()
        barangay = str(r.get("barangay") or "").strip()
        organization = coop or fed or barangay or "Guimaras"

        items.append(
            {
                "farmer_id": int(r.get("farmer_id") or 0),
                "first_name": first,
                "last_name": last,
                "barangay": barangay,
                "organization": organization,
                "federation_assoc": fed,
                "coop_name": coop,
                "profile_photo": str(r.get("profile_photo") or "").strip(),
                "status": str(r.get("status") or "active"),
                "phone_number": str(r.get("phone_number") or "").strip(),
                "email": str(r.get("email") or "").strip(),
                "username": str(r.get("username") or "").strip(),
            }
        )

    def _sort_key(item: dict) -> tuple:
        name = f"{item.get('first_name', '')} {item.get('last_name', '')}".lower()
        org = str(item.get("organization") or "").lower()
        is_arnold = "arnold" in name
        is_officer = "officer" in org or str(item.get("federation_assoc") or "").lower() == "officer"
        return (0 if is_arnold else 1, 0 if is_officer else 1, name)

    items.sort(key=_sort_key)
    return items


def _client_web_public_base() -> str:
    from beanthentic_url_config import resolve_client_web_base

    try:
        return resolve_client_web_base(request.host)
    except RuntimeError:
        return resolve_client_web_base("")


def handle_client_farmer_profile() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    try:
        farmer_id = int(request.args.get("farmer_id") or 0)
    except (TypeError, ValueError):
        farmer_id = 0
    if farmer_id <= 0:
        return _json_response({"ok": False, "error": "farmer_id is required"}, 400)
    conn = None
    try:
        conn = _connect()
        with conn.cursor() as cur:
            cur.execute(
                """
                SELECT
                  f.farmer_id,
                  f.status,
                  f.profile_photo,
                  u.phone_number,
                  u.email,
                  u.username,
                  pi.first_name,
                  pi.last_name,
                  pi.birthday,
                  pi.contact_number,
                  pi.province,
                  pi.municipality,
                  pi.barangay AS pi_barangay,
                  pi.current_address,
                  COALESCE(pi.barangay, fi.barangay) AS barangay,
                  fi.farm_name,
                  fi.ownership_status,
                  ai.federation_assoc,
                  ai.rsbsa_registered,
                  ai.rsbsa_number,
                  ai.rsbsa_status
                FROM farmers f
                INNER JOIN users u ON u.user_id = f.user_id
                LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
                LEFT JOIN farm_information fi ON fi.farmer_id = f.farmer_id
                LEFT JOIN affiliation_information ai ON ai.farmer_id = f.farmer_id
                WHERE f.farmer_id = %s
                LIMIT 1
                """,
                (farmer_id,),
            )
            row = cur.fetchone()
        if not row:
            return _json_response({"ok": False, "error": "Farmer not found"}, 404)
        farmer = dict(row)
        farmer.pop("phone_number", None)
        farmer.pop("email", None)
        farmer.pop("contact_number", None)
        addr = str(farmer.get("current_address") or "").strip()
        if not addr:
            parts = [
                str(farmer.get("pi_barangay") or "").strip(),
                str(farmer.get("municipality") or "").strip(),
                str(farmer.get("province") or "").strip(),
            ]
            addr = ", ".join(p for p in parts if p)
        farmer["current_address"] = addr or "—"
        bday = farmer.get("birthday")
        if bday is not None and hasattr(bday, "strftime"):
            farmer["birthday"] = bday.strftime("%B %d, %Y")
        first = str(farmer.get("first_name") or "").strip()
        last = str(farmer.get("last_name") or "").strip()
        farmer["display_name"] = f"{first} {last}".strip() or f"Farmer #{farmer_id}"
        return _json_response({"ok": True, "farmer": farmer}, 200)
    except Exception as e:
        return _json_response({"ok": False, "error": str(e)}, 500)
    finally:
        if conn:
            conn.close()


def handle_client_profile_url() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    try:
        farmer_id = int(request.args.get("farmer_id") or 0)
    except (TypeError, ValueError):
        farmer_id = 0
    if farmer_id <= 0:
        return _json_response({"ok": False, "error": "farmer_id is required"}, 400)
    from beanthentic_url_config import build_farmer_profile_url

    try:
        http_host = request.host
    except RuntimeError:
        http_host = ""
    url = build_farmer_profile_url(http_host, farmer_id)
    return _json_response({"ok": True, "farmer_id": farmer_id, "profile_url": url}, 200)


def handle_client_farmers() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    conn = None
    try:
        conn = _connect()
        farmers = _client_farmers_items(conn)
        return _json_response({"ok": True, "farmers": farmers}, 200)
    except Exception as e:
        return _json_response({"ok": False, "error": str(e), "farmers": []}, 500)
    finally:
        if conn:
            conn.close()


def _farmer_display_name_from_row(r: dict) -> str:
    first = str(r.get("first_name") or "").strip()
    last = str(r.get("last_name") or "").strip()
    full = f"{first} {last}".strip()
    if full:
        return full
    un = str(r.get("username") or "").strip()
    if un:
        return un
    fid = int(r.get("farmer_id") or 0)
    return f"Farmer #{fid}" if fid > 0 else "Farmer"


def handle_client_transaction_farmers() -> Response:
    """GET — distinct farmers linked to customer_transaction rows for this buyer name."""
    if request.method == "OPTIONS":
        return _preflight_ok()
    client_name = str(request.args.get("client_name") or request.args.get("buyer_name") or "").strip()
    if not client_name:
        return _json_response({"ok": False, "error": "client_name is required."}, 400)

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        cur.execute(
            """
            SELECT ct.farmer_id,
                   MAX(ct.transaction_date) AS last_transaction_at,
                   COUNT(*) AS tx_count,
                   f.farm_code,
                   u.username,
                   pi.first_name,
                   pi.last_name
            FROM customer_transaction ct
            INNER JOIN farmers f ON f.farmer_id = ct.farmer_id
            LEFT JOIN users u ON u.user_id = f.user_id
            LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
            WHERE LOWER(TRIM(ct.buyer_name)) = LOWER(TRIM(%s))
            GROUP BY ct.farmer_id, f.farm_code, u.username, pi.first_name, pi.last_name
            ORDER BY last_transaction_at DESC, ct.farmer_id DESC
            """,
            (client_name,),
        )
        rows = cur.fetchall() or []
        farmers: list[dict] = []
        for r in rows:
            fid = int(r.get("farmer_id") or 0)
            if fid <= 0:
                continue
            farm_code = str(r.get("farm_code") or "").strip()
            farmers.append(
                {
                    "farmer_id": fid,
                    "farmer_name": _farmer_display_name_from_row(r),
                    "farmer_no": farm_code or str(fid),
                    "tx_count": int(r.get("tx_count") or 0),
                    "last_transaction_at": (
                        r["last_transaction_at"].isoformat()
                        if hasattr(r.get("last_transaction_at"), "isoformat")
                        else str(r.get("last_transaction_at") or "")
                    ),
                }
            )
        return _json_response(
            {"ok": True, "client_name": client_name, "farmers": farmers, "count": len(farmers)},
            200,
        )
    except Exception as e:
        return _json_response({"ok": False, "error": str(e), "farmers": []}, 500)
    finally:
        if conn:
            conn.close()

def handle_admin_farmer_data() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    conn = None
    try:
        conn = _connect()
        _ensure_shared_messages_table(conn)
        items = _admin_farmer_data_items(conn)
        return _json_response({"ok": True, "items": items}, 200)
    except Exception as e:
        return _json_response({"ok": False, "error": str(e)}, 500)
    finally:
        if conn:
            conn.close()


def handle_admin_customer_transactions() -> Response:
    """Admin dashboard — all approved/sent transactions (same data as app History)."""
    if request.method == "OPTIONS":
        return _preflight_ok()
    try:
        limit = int(request.args.get("limit") or 500)
    except (TypeError, ValueError):
        limit = 500
    limit = max(1, min(limit, 800))
    try:
        farmer_id = int(request.args.get("farmer_id") or 0)
    except (TypeError, ValueError):
        farmer_id = 0

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        sql = """
            SELECT
              ct.customer_transaction_id,
              ct.farmer_id,
              ct.buyer_name,
              ct.product,
              ct.quantity,
              ct.amount,
              ct.payment_amount,
              ct.payment_method,
              ct.reference_no,
              ct.transaction_date,
              f.farm_code,
              u.username,
              u.phone_number,
              pi.first_name,
              pi.last_name,
              (
                SELECT th.status
                FROM transaction_history th
                WHERE th.customer_transaction_id = ct.customer_transaction_id
                ORDER BY th.transaction_history_id DESC
                LIMIT 1
              ) AS current_status,
              (
                SELECT th.created_at
                FROM transaction_history th
                WHERE th.customer_transaction_id = ct.customer_transaction_id
                  AND th.status = 'approved'
                ORDER BY th.transaction_history_id ASC
                LIMIT 1
              ) AS approved_at
            FROM customer_transaction ct
            LEFT JOIN farmers f ON f.farmer_id = ct.farmer_id
            LEFT JOIN users u ON u.user_id = f.user_id
            LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
            WHERE (
              SELECT th.status
              FROM transaction_history th
              WHERE th.customer_transaction_id = ct.customer_transaction_id
              ORDER BY th.transaction_history_id DESC
              LIMIT 1
            ) IN ('approved', 'sent_to_client')
        """
        params: list = []
        if farmer_id > 0:
            sql += " AND ct.farmer_id = %s"
            params.append(farmer_id)
        sql += """
            ORDER BY COALESCE(approved_at, ct.transaction_date) DESC,
                     ct.customer_transaction_id DESC
            LIMIT %s
        """
        params.append(limit)
        cur.execute(sql, tuple(params))
        rows = cur.fetchall() or []
        items = []
        for r in rows:
            qty = float(r.get("quantity") or 0)
            fid = int(r.get("farmer_id") or 0)
            farm_code = str(r.get("farm_code") or "").strip()
            farmer_no = farm_code if farm_code else (str(fid) if fid else "")
            fn = str(r.get("first_name") or "").strip()
            ln = str(r.get("last_name") or "").strip()
            name = (fn + " " + ln).strip()
            if not name:
                name = str(r.get("username") or "").strip()
            if not name:
                name = str(r.get("phone_number") or "").strip()
            product = str(r.get("product") or "").strip()
            variety = product.lower()
            if variety not in ("liberica", "excelsa", "robusta"):
                variety = product.lower()
            at = r.get("approved_at") or r.get("transaction_date")
            status = str(r.get("current_status") or "approved").strip().lower()
            items.append(
                {
                    "id": int(r.get("customer_transaction_id") or 0),
                    "customer_transaction_id": int(r.get("customer_transaction_id") or 0),
                    "farmer_id": fid,
                    "farmer_no": farmer_no,
                    "farmer_name": name,
                    "recorded_at": str(at) if at else "",
                    "variety": variety,
                    "delta_kg": abs(qty),
                    "payment_amount": float(r.get("payment_amount") or 0),
                    "payment_method": str(r.get("payment_method") or "Cash").strip() or "Cash",
                    "reference_no": str(r.get("reference_no") or "").strip(),
                    "buyer_name": str(r.get("buyer_name") or "").strip(),
                    "notes": "",
                    "recorded_by_phone": "",
                    "status": status,
                    "sent_to_client": status == "sent_to_client",
                }
            )
        return _json_response({"ok": True, "items": items, "count": len(items)}, 200)
    except Exception as e:
        return _json_response({"ok": False, "error": f"admin_customer_transactions failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_admin_client_reports() -> Response:
    """Admin Client Report — list rows from client_misconduct_report."""
    if request.method == "OPTIONS":
        return _preflight_ok()
    try:
        limit = int(request.args.get("limit") or 500)
    except (TypeError, ValueError):
        limit = 500
    limit = max(1, min(limit, 1000))
    status = str(request.args.get("status") or "").strip()
    q = str(request.args.get("q") or "").strip()

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        _ensure_client_report_table(cur)
        sql = "SELECT * FROM client_misconduct_report WHERE 1=1"
        args: list = []
        if status:
            norm = status.lower().replace("_", " ")
            if norm == "open":
                norm = "under review"
            sql += " AND LOWER(REPLACE(status, '_', ' ')) = %s"
            args.append(norm)
        if q:
            like = f"%{q}%"
            sql += (
                " AND (reporter_name LIKE %s OR reporter_contact LIKE %s OR farmer_name LIKE %s"
                " OR reason_category LIKE %s OR reason_detail LIKE %s OR allegation LIKE %s)"
            )
            args.extend([like] * 6)
        sql += " ORDER BY created_at DESC, report_id DESC LIMIT %s"
        args.append(int(limit))
        cur.execute(sql, tuple(args))
        rows = cur.fetchall() or []
        items = []
        for r in rows:
            rid = int(r.get("report_id") or 0)
            st = str(r.get("status") or "under review").strip().lower().replace("_", " ")
            if st == "open":
                st = "under review"
            at = r.get("created_at")
            items.append(
                {
                    "id": rid,
                    "report_id": rid,
                    "created_at": at.isoformat() if hasattr(at, "isoformat") else str(at or ""),
                    "reporter_name": str(r.get("reporter_name") or "").strip(),
                    "reporter_contact": str(r.get("reporter_contact") or "").strip(),
                    "reason_category": str(r.get("reason_category") or "").strip(),
                    "reason_detail": str(r.get("reason_detail") or "").strip(),
                    "allegation": str(r.get("allegation") or "").strip(),
                    "farmer_id": int(r["farmer_id"]) if r.get("farmer_id") else None,
                    "farmer_no": r.get("farmer_no"),
                    "farmer_name": str(r.get("farmer_name") or "").strip() or "—",
                    "status": st,
                }
            )
        return _json_response({"ok": True, "items": items, "count": len(items)}, 200)
    except Exception as e:
        return _json_response({"ok": False, "error": f"admin_client_reports failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_signup() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
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
    pw_hash = bcrypt.hashpw(password.encode("utf-8"), bcrypt.gensalt(rounds=10)).decode("utf-8")

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
    if request.method == "OPTIONS":
        return _preflight_ok()
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
                           f.status AS farmer_status,
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
                           f.status AS farmer_status,
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

        fid_check = int(row.get("farmer_id") or 0)
        if fid_check > 0:
            acct = _farmer_account_status_inline(conn, fid_check)
            if acct.get("is_suspended"):
                msg = "Your account is suspended. You cannot use the app until access is restored."
                until = acct.get("suspended_until") or ""
                reason = str(acct.get("suspension_reason") or "").strip()
                if until:
                    msg += f" Access restores after {until}."
                if reason:
                    msg += f" Reason: {reason}"
                return _json_response({"ok": False, "error": msg}, 403)

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
        fs = str(row.get("farmer_status") or "pending").strip().lower()
        reg_complete = fs == "active"
        return _json_response(
            {
                "ok": True,
                "user": {
                    "user_id": int(row["user_id"]),
                    "farmer_id": int(fid) if fid is not None else None,
                    "farmer_status": str(row.get("farmer_status") or "pending"),
                    "registration_complete": reg_complete,
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
    if request.method == "OPTIONS":
        return _preflight_ok()
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
            if cur.fetchone() is None:
                return _json_response({"ok": False}, 200)

            cur.execute(
                "SELECT farmer_id FROM farmers WHERE user_id = %s LIMIT 1",
                (user_id,),
            )
            farmer_row = cur.fetchone()
            farmer_id = int(farmer_row["farmer_id"]) if farmer_row and farmer_row.get("farmer_id") else 0
            if farmer_id > 0:
                acct = _farmer_account_status_inline(conn, farmer_id)
                if acct.get("is_suspended"):
                    msg = "Your account is suspended. You cannot use the app until access is restored."
                    until = acct.get("suspended_until") or ""
                    reason = str(acct.get("suspension_reason") or "").strip()
                    if until:
                        msg += f" Access restores after {until}."
                    if reason:
                        msg += f" Reason: {reason}"
                    return _json_response(
                        {"ok": False, "reason": "suspended", "message": msg},
                        200,
                    )
        return _json_response({"ok": True}, 200)
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
    """Store same choices as the register-farm web form / VARCHAR column."""
    s = (raw or "").strip().lower()
    wizard = frozenset(
        {"landowner", "cloa_holder", "list_holder", "sessional_farm_worker", "others"}
    )
    if s in wizard:
        return s
    legacy = {
        "owner": "landowner",
        "owned": "landowner",
        "tenant": "sessional_farm_worker",
        "lessee": "sessional_farm_worker",
        "co-owner": "cloa_holder",
        "co_owner": "cloa_holder",
        "coowner": "cloa_holder",
        "other": "others",
        "usufruct": "others",
    }
    return legacy.get(s, "others")


def _fr_ncfrs_tiny(raw: str) -> int:
    return 1 if (raw or "").strip().lower() == "yes" else 0


def _fr_rsbsa_code(raw: str) -> int:
    s = (raw or "").strip().lower()
    if s == "yes":
        return 1
    if s == "pending":
        return 2
    return 0


def _rsbsa_label_from_db(val) -> str:
    try:
        iv = int(val or 0)
    except (TypeError, ValueError):
        return "no"
    if iv == 1:
        return "yes"
    if iv == 2:
        return "pending"
    return "no"


def _rsbsa_status_value(raw: str):
    s = (raw or "").strip().lower()
    if s in ("not_yet_applied", "pending_rsbsa"):
        return s
    return None


def _rsbsa_status_label(raw: str) -> str:
    s = (raw or "").strip().lower()
    if s == "not_yet_applied":
        return "Not Yet Applied"
    if s == "pending_rsbsa":
        return "Pending RSBSA"
    return ""


def _validate_birthday_field(d: dict, err: dict) -> None:
    birthday = str(d.get("birthday") or "").strip()
    if not birthday:
        err["birthday"] = "Enter your birthday."
        return
    try:
        dob = datetime.strptime(birthday[:10], "%Y-%m-%d").date()
    except ValueError:
        err["birthday"] = "Enter a valid date (YYYY-MM-DD)."
        return
    today = datetime.now().date()
    if dob > today:
        err["birthday"] = "Birthday cannot be in the future."
    elif dob.year < 1900:
        err["birthday"] = "Enter a valid birthday."


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
    _validate_birthday_field(d, err)
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
    if rsb not in ("yes", "no"):
        err["rsbsa_registered"] = "Select RSBSA Registered (Yes or No)."
    rsb_num = str(d.get("rsbsa_number") or "").strip()
    rsb_status = str(d.get("rsbsa_status") or "").strip().lower()
    if rsb == "yes" and len(rsb_num) < 4:
        err["rsbsa_number"] = "Enter your RSBSA number."
    if rsb == "no" and rsb_status not in ("not_yet_applied", "pending_rsbsa"):
        err["rsbsa_status"] = "Select RSBSA Status."
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


def _resolve_user_id_from_body(conn, body: dict) -> int:
    try:
        user_id = int(body.get("user_id") or 0)
    except (TypeError, ValueError):
        user_id = 0
    if user_id > 0:
        return user_id
    phone = normalize_phone(str(body.get("phone") or "").strip())
    if phone:
        with conn.cursor() as cur:
            cur.execute(
                "SELECT user_id FROM users WHERE phone_number = %s AND is_active = 1 LIMIT 1",
                (phone,),
            )
            row = cur.fetchone()
            if row:
                return int(row["user_id"])
    email = str(body.get("email") or "").strip()
    if email:
        with conn.cursor() as cur:
            cur.execute(
                "SELECT user_id FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(%s)) AND is_active = 1 LIMIT 1",
                (email,),
            )
            row = cur.fetchone()
            if row:
                return int(row["user_id"])
    return 0


def farmer_mysql_save_py(conn, user_id: int, body: dict) -> int:
    cur = conn.cursor()
    cur.execute("SELECT user_id FROM users WHERE user_id = %s AND is_active = 1 LIMIT 1", (user_id,))
    if not cur.fetchone():
        raise RuntimeError("Invalid user.")

    first = str(body.get("first_name") or "").strip()
    last = str(body.get("last_name") or "").strip()
    birthday = str(body.get("birthday") or "").strip()
    birthday_sql = birthday if birthday else None
    phone = normalize_phone(str(body.get("phone") or "").strip())
    phone_sql = phone if phone else None
    barangay = str(body.get("barangay") or "").strip()
    farm_addr = str(body.get("farm_address") or "").strip()
    province = str(body.get("province") or "").strip() or "Batangas"
    municipality = str(body.get("municipality") or "").strip() or "Lipa City"
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
    ncfrs_raw = str(body.get("ncfrs") or "")
    rsb = str(body.get("rsbsa_registered") or "")
    rsb_no = str(body.get("rsbsa_number") or "").strip()
    rsb_status = _rsbsa_status_value(str(body.get("rsbsa_status") or ""))
    if (rsb or "").strip().lower() == "no":
        rsb_no = "N/A"
    else:
        rsb_status = None

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
                   birthday = %s, contact_number = COALESCE(%s, contact_number), 
                   barangay = %s, province = %s, municipality = %s, current_address = COALESCE(%s, current_address) 
                   WHERE farmer_id = %s""",
                (first or None, last or None, birthday_sql, phone_sql, barangay or None, province, municipality, addr_line, farmer_id),
            )
        else:
            cur.execute(
                """INSERT INTO personal_information (farmer_id, first_name, last_name, birthday, contact_number, barangay, province, municipality, current_address)
                   VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)""",
                (farmer_id, first or None, last or None, birthday_sql, phone_sql, barangay or None, province, municipality, addr_line),
            )

        cur.execute("SELECT farm_info_id FROM farm_information WHERE farmer_id = %s LIMIT 1", (farmer_id,))
        if cur.fetchone():
            cur.execute(
                """UPDATE farm_information SET ownership_status = %s, farm_address = COALESCE(%s, farm_address),
                   barangay = COALESCE(%s, barangay), farm_size_ha = COALESCE(%s, farm_size_ha), province = %s, municipality = %s WHERE farmer_id = %s""",
                (ownership, addr_line, barangay or None, plant_ha, province, municipality, farmer_id),
            )
        else:
            cur.execute(
                """INSERT INTO farm_information (farmer_id, ownership_status, farm_address, barangay, farm_size_ha, province, municipality)
                   VALUES (%s, %s, %s, %s, %s, %s, %s)""",
                (farmer_id, ownership, addr_line, barangay or None, plant_ha, province, municipality),
            )

        cur.execute("SELECT affiliation_info_id FROM affiliation_information WHERE farmer_id = %s LIMIT 1", (farmer_id,))
        if cur.fetchone():
            cur.execute(
                """UPDATE affiliation_information SET federation_assoc = %s, coop_name = %s,
                   ncfrs = %s, rsbsa_registered = %s, rsbsa_number = %s, rsbsa_status = %s WHERE farmer_id = %s""",
                (
                    fed or None,
                    assoc or None,
                    _fr_ncfrs_tiny(ncfrs_raw),
                    _fr_rsbsa_code(rsb),
                    rsb_no or None,
                    rsb_status,
                    farmer_id,
                ),
            )
        else:
            cur.execute(
                """INSERT INTO affiliation_information (farmer_id, federation_assoc, coop_name, ncfrs, rsbsa_registered, rsbsa_number, rsbsa_status)
                   VALUES (%s, %s, %s, %s, %s, %s, %s)""",
                (
                    farmer_id,
                    fed or None,
                    assoc or None,
                    _fr_ncfrs_tiny(ncfrs_raw),
                    _fr_rsbsa_code(rsb),
                    rsb_no or None,
                    rsb_status,
                ),
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

        photo_path = _mysql_save_profile_photo_file(
            farmer_id, str(body.get("profile_photo_data") or "")
        )
        if photo_path:
            cur.execute(
                "UPDATE farmers SET profile_photo = %s WHERE farmer_id = %s",
                (photo_path, farmer_id),
            )

        conn.commit()
        return farmer_id
    except Exception:
        conn.rollback()
        raise


def handle_register_farm_farmer() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    if request.method != "POST":
        r = jsonify({"success": False, "errors": {"_error": "Method not allowed"}})
        r.status_code = 405
        _cors_headers(r)
        return r
    body = request.get_json(silent=True) or {}
    conn = None
    try:
        conn = _connect()
        user_id = _resolve_user_id_from_body(conn, body)
    except Exception as e:
        r = jsonify({"success": False, "errors": {"_error": f"Database unavailable: {e!s}"}})
        r.status_code = 503
        _cors_headers(r)
        return r
    errors = validate_farmer_payload_py(body, user_id)
    if errors:
        r = jsonify({"success": False, "errors": errors})
        r.status_code = 400
        _cors_headers(r)
        return r
    try:
        farmer_id = farmer_mysql_save_py(conn, user_id, body)
        r = jsonify(
            {
                "success": True,
                "farmer_id": farmer_id,
                "user_id": user_id,
                "message": "Farmer registered successfully",
            }
        )
        r.status_code = 200
        _cors_headers(r)
        return r
    except RuntimeError as e:
        r = jsonify({"success": False, "errors": {"_error": str(e)}})
        r.status_code = 400
        _cors_headers(r)
        return r
    except Exception as e:
        r = jsonify({"success": False, "errors": {"_error": str(e)}})
        r.status_code = 500
        _cors_headers(r)
        return r
    finally:
        if conn:
            conn.close()


def handle_get_farmer_profile() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()

    login_id = (request.args.get("login_id") or "").strip()
    try:
        user_id_arg = int(request.args.get("user_id") or 0)
    except (TypeError, ValueError):
        user_id_arg = 0
    try:
        farmer_id_arg = int(request.args.get("farmer_id") or 0)
    except (TypeError, ValueError):
        farmer_id_arg = 0
    if not login_id and user_id_arg <= 0 and farmer_id_arg <= 0:
        return _json_response({"success": False, "error": "Missing login_id or user_id"}, 400)

    conn = None
    try:
        conn = _connect()
        user_id = user_id_arg
        if user_id <= 0 and login_id:
            user_id = _resolve_user_id_from_body(
                conn, {"login": login_id, "phone": login_id, "email": login_id}
            )
        if user_id <= 0 and farmer_id_arg <= 0:
            return _json_response({"success": True, "found": False, "profile": None})

        # Fetch profile data
        with conn.cursor() as cur:
            if farmer_id_arg > 0:
                cur.execute(
                    """
                    SELECT 
                        f.farmer_id, f.profile_photo, u.email, u.phone_number,
                        pi.first_name, pi.last_name, pi.birthday, pi.barangay,
                        pi.province, pi.municipality,
                        fi.ownership_status, fi.farm_size_ha,
                        ai.federation_assoc, ai.coop_name, ai.ncfrs, ai.rsbsa_registered, ai.rsbsa_number, ai.rsbsa_status,
                        tc.robusta_bearing, tc.robusta_non_bearing, tc.liberica_bearing, tc.liberica_non_bearing, tc.excelsa_bearing, tc.excelsa_non_bearing,
                        prod.robusta_qty_kg, prod.liberica_qty_kg, prod.excelsa_qty_kg, prod.production_year
                    FROM farmers f
                    INNER JOIN users u ON u.user_id = f.user_id
                    LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
                    LEFT JOIN farm_information fi ON fi.farmer_id = f.farmer_id
                    LEFT JOIN affiliation_information ai ON ai.farmer_id = f.farmer_id
                    LEFT JOIN tree_counts tc ON tc.farmer_id = f.farmer_id
                    LEFT JOIN production_information prod ON prod.farmer_id = f.farmer_id
                    WHERE f.farmer_id = %s
                    LIMIT 1
                    """,
                    (farmer_id_arg,),
                )
            else:
                cur.execute(
                    """
                    SELECT 
                        f.farmer_id, f.profile_photo, u.email, u.phone_number,
                        pi.first_name, pi.last_name, pi.birthday, pi.barangay,
                        pi.province, pi.municipality,
                        fi.ownership_status, fi.farm_size_ha,
                        ai.federation_assoc, ai.coop_name, ai.ncfrs, ai.rsbsa_registered, ai.rsbsa_number, ai.rsbsa_status,
                        tc.robusta_bearing, tc.robusta_non_bearing, tc.liberica_bearing, tc.liberica_non_bearing, tc.excelsa_bearing, tc.excelsa_non_bearing,
                        prod.robusta_qty_kg, prod.liberica_qty_kg, prod.excelsa_qty_kg, prod.production_year
                    FROM farmers f
                    INNER JOIN users u ON u.user_id = f.user_id
                    LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
                    LEFT JOIN farm_information fi ON fi.farmer_id = f.farmer_id
                    LEFT JOIN affiliation_information ai ON ai.farmer_id = f.farmer_id
                    LEFT JOIN tree_counts tc ON tc.farmer_id = f.farmer_id
                    LEFT JOIN production_information prod ON prod.farmer_id = f.farmer_id
                    WHERE f.user_id = %s
                    ORDER BY f.farmer_id DESC LIMIT 1
                    """,
                    (user_id,),
                )
            row = cur.fetchone()

        if not row:
            return _json_response({"success": True, "found": False, "profile": None})

        profile = {
            "id": row["farmer_id"],
            "first_name": row["first_name"] or "",
            "last_name": row["last_name"] or "",
            "birthday": _fmt_birthday(row["birthday"]),
            "email": row["email"] or "",
            "phone": row["phone_number"] or "",
            "province": row["province"] or "Batangas",
            "municipality": row["municipality"] or "Lipa City",
            "barangay": row["barangay"] or "",
            "federation": row["federation_assoc"] or "",
            "association": row["coop_name"] or "",
            "ncfrs": "yes" if int(row.get("ncfrs") or 0) == 1 else "no",
            "rsbsa_registered": _rsbsa_label_from_db(row.get("rsbsa_registered")),
            "rsbsa_number": row["rsbsa_number"] or "",
            "rsbsa_status": _rsbsa_status_label(row.get("rsbsa_status") or ""),
            "ownership_status": row["ownership_status"] or "",
            "plant_area_value": row["farm_size_ha"],
            "plant_area_unit": "ha",
            "tree_counts": {
                "liberica": {
                    "bearing": row["liberica_bearing"],
                    "non_bearing": row["liberica_non_bearing"],
                },
                "robusta": {
                    "bearing": row["robusta_bearing"],
                    "non_bearing": row["robusta_non_bearing"],
                },
                "excelsa": {
                    "bearing": row["excelsa_bearing"],
                    "non_bearing": row["excelsa_non_bearing"],
                },
            },
            "production": {
                "liberica": {"qty": row["liberica_qty_kg"], "unit": "kg"},
                "robusta": {"qty": row["robusta_qty_kg"], "unit": "kg"},
                "excelsa": {"qty": row["excelsa_qty_kg"], "unit": "kg"},
            },
            "profile_photo_data": row["profile_photo"] or "",
        }
        return _json_response({"success": True, "found": True, "profile": profile})

    except Exception as e:
        return _json_response({"success": False, "error": str(e)}, 500)
    finally:
        if conn:
            conn.close()


def handle_farmer_profile_update() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()

    if request.method != "POST":
        return _json_response({"success": False, "error": "Method not allowed"}, 405)

    body = request.get_json(silent=True) or {}
    conn = None
    try:
        conn = _connect()
        user_id = int(body.get("user_id") or 0)
        if user_id <= 0:
            user_id = _resolve_user_id_from_body(
                conn, {"login": body.get("login"), "phone": body.get("phone"), "email": body.get("email")}
            )
        if user_id <= 0:
            return _json_response({"success": False, "error": "Missing or invalid user"}, 400)

        with conn.cursor() as cur:
            cur.execute(
                """
                SELECT f.farmer_id
                FROM farmers f
                INNER JOIN users u ON u.user_id = f.user_id
                WHERE f.user_id = %s AND u.is_active = 1
                ORDER BY f.farmer_id DESC
                LIMIT 1
                """,
                (user_id,),
            )
            row = cur.fetchone()
        if not row:
            return _json_response(
                {"success": False, "error": "Farmer profile not found. Complete Register Farm first."},
                404,
            )

        farmer_id = int(row["farmer_id"])
        updated = False
        profile_photo = ""

        photo_data = str(body.get("profile_photo_data") or "").strip()
        if photo_data:
            photo_path = _mysql_save_profile_photo_file(farmer_id, photo_data)
            if not photo_path:
                return _json_response(
                    {"success": False, "error": "Invalid profile photo. Use JPG, PNG, or WebP."},
                    400,
                )
            with conn.cursor() as cur:
                cur.execute(
                    "UPDATE farmers SET profile_photo = %s, updated_at = NOW() WHERE farmer_id = %s",
                    (photo_path, farmer_id),
                )
            conn.commit()
            profile_photo = photo_path
            updated = True

        first = str(body.get("first_name") or "").strip()
        last = str(body.get("last_name") or "").strip()
        if first or last:
            with conn.cursor() as cur:
                cur.execute(
                    "SELECT personal_info_id FROM personal_information WHERE farmer_id = %s LIMIT 1",
                    (farmer_id,),
                )
                pi = cur.fetchone()
                if pi:
                    cur.execute(
                        """
                        UPDATE personal_information
                        SET first_name = COALESCE(NULLIF(%s, ''), first_name),
                            last_name = COALESCE(NULLIF(%s, ''), last_name)
                        WHERE farmer_id = %s
                        """,
                        (first, last, farmer_id),
                    )
                elif first and last:
                    cur.execute(
                        """
                        INSERT INTO personal_information (farmer_id, first_name, last_name)
                        VALUES (%s, %s, %s)
                        """,
                        (farmer_id, first, last),
                    )
            conn.commit()
            updated = True

        if not updated:
            return _json_response({"success": False, "error": "Nothing to update"}, 400)

        if not profile_photo:
            with conn.cursor() as cur:
                cur.execute(
                    "SELECT profile_photo FROM farmers WHERE farmer_id = %s LIMIT 1",
                    (farmer_id,),
                )
                ph = cur.fetchone()
            profile_photo = str((ph or {}).get("profile_photo") or "").strip()

        return _json_response(
            {
                "success": True,
                "farmer_id": farmer_id,
                "user_id": user_id,
                "profile_photo": profile_photo,
                "first_name": first,
                "last_name": last,
            }
        )
    except Exception as e:
        if conn:
            try:
                conn.rollback()
            except Exception:
                pass
        return _json_response({"success": False, "error": str(e)}, 500)
    finally:
        if conn:
            conn.close()


def handle_send_receipt() -> Response:
    if request.method == "OPTIONS":
        return _json_response({"ok": True})

    body = request.get_json(silent=True) or {}
    user_id = int(body.get("user_id") or 0)
    if user_id <= 0:
        return _json_response({"ok": False, "error": "Missing account. Log in again."}, 401)

    ref = str(body.get("ref") or "").strip()
    buyer = str(body.get("buyer") or "").strip()
    product = str(body.get("product") or "").strip()
    if not ref or not buyer:
        return _json_response({"ok": False, "error": "Receipt reference and buyer name are required."}, 400)

    qty_raw = str(body.get("qty") or "").strip()
    try:
        qty = float(qty_raw) if qty_raw else 0.0
    except (TypeError, ValueError):
        qty = 0.0
    amount = float(body.get("amount") or body.get("total") or 0)
    payment_amount = float(body.get("paymentAmount") or 0)
    payment_method = str(body.get("payment") or body.get("paymentMethod") or "Cash").strip() or "Cash"

    at_raw = str(body.get("at") or "").strip()
    txn_date = datetime.now()
    if at_raw:
        try:
            txn_date = datetime.fromisoformat(at_raw.replace("Z", "+00:00"))
        except ValueError:
            pass

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        cur.execute("SELECT farmer_id FROM farmers WHERE user_id = %s LIMIT 1", (user_id,))
        farmer_row = cur.fetchone()
        if not farmer_row:
            return _json_response(
                {"ok": False, "error": "Farmer profile not found. Complete registration first."},
                404,
            )
        farmer_id = int(farmer_row["farmer_id"])

        client_id = None
        client_name = buyer
        cur.execute(
            """SELECT client_id,
                      COALESCE(NULLIF(TRIM(company_name), ''), NULLIF(TRIM(full_name), '')) AS display_name
               FROM client
               WHERE TRIM(full_name) = %s OR TRIM(company_name) = %s
               LIMIT 1""",
            (buyer, buyer),
        )
        client_row = cur.fetchone()
        if client_row:
            client_id = int(client_row["client_id"])
            client_name = str(client_row.get("display_name") or buyer).strip() or buyer

        product_val = product if product else "Coffee"
        cur.execute(
            """SELECT customer_transaction_id FROM customer_transaction
               WHERE farmer_id = %s AND reference_no = %s LIMIT 1""",
            (farmer_id, ref),
        )
        ex_row = cur.fetchone()

        if ex_row:
            tx_id = int(ex_row["customer_transaction_id"])
            cur.execute(
                """UPDATE customer_transaction SET client_id = %s, buyer_name = %s, product = %s,
                   quantity = %s, amount = %s, payment_amount = %s, payment_method = %s,
                   transaction_date = %s WHERE customer_transaction_id = %s""",
                (
                    client_id,
                    buyer,
                    product_val,
                    qty,
                    amount,
                    payment_amount,
                    payment_method,
                    txn_date,
                    tx_id,
                ),
            )
        else:
            cur.execute(
                """INSERT INTO customer_transaction
                   (farmer_id, client_id, buyer_name, product, quantity, amount, payment_amount,
                    payment_method, reference_no, transaction_date)
                   VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)""",
                (
                    farmer_id,
                    client_id,
                    buyer,
                    product_val,
                    qty,
                    amount,
                    payment_amount,
                    payment_method,
                    ref,
                    txn_date,
                ),
            )
            tx_id = int(cur.lastrowid)

        cur.execute(
            """INSERT INTO transaction_history
               (customer_transaction_id, status, remarks, changed_by_user_id)
               VALUES (%s, %s, %s, %s)""",
            (tx_id, "sent_to_client", f"Receipt sent to client: {client_name}", user_id),
        )
        conn.commit()

        return _json_response(
            {
                "ok": True,
                "message": "You have successfully sent the receipt.",
                "customer_transaction_id": tx_id,
                "client_name": client_name,
                "sent_to_client": True,
            }
        )
    except Exception as e:
        if conn:
            try:
                conn.rollback()
            except Exception:
                pass
        return _json_response({"ok": False, "error": f"send_receipt failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def _first_active_farmer_id(cur) -> int:
    cur.execute(
        "SELECT farmer_id FROM farmers WHERE status = 'active' ORDER BY farmer_id ASC LIMIT 1"
    )
    row = cur.fetchone()
    if row and int(row.get("farmer_id") or 0) > 0:
        return int(row["farmer_id"])
    cur.execute("SELECT farmer_id FROM farmers ORDER BY farmer_id ASC LIMIT 1")
    row = cur.fetchone()
    return int(row["farmer_id"]) if row else 0


def _resolve_farmer_id_for_client_tx(cur, form) -> int:
  """Use farmer_id / farmer_name from Client Web. Never assign another farmer silently."""
  try:
    requested = int(str(form.get("farmer_id") or "0"))
  except (TypeError, ValueError):
    requested = 0
  if requested > 0:
    cur.execute("SELECT farmer_id FROM farmers WHERE farmer_id = %s LIMIT 1", (requested,))
    row = cur.fetchone()
    if row and int(row.get("farmer_id") or 0) > 0:
      return int(row["farmer_id"])

  name = " ".join(str(form.get("farmer_name") or "").split())
  if name:
    cur.execute(
      """
      SELECT f.farmer_id
      FROM farmers f
      INNER JOIN personal_information pi ON pi.farmer_id = f.farmer_id
      WHERE LOWER(TRIM(CONCAT(COALESCE(pi.first_name, ''), ' ', COALESCE(pi.last_name, '')))) = LOWER(%s)
      ORDER BY f.farmer_id ASC
      LIMIT 1
      """,
      (name,),
    )
    row = cur.fetchone()
    if row and int(row.get("farmer_id") or 0) > 0:
      return int(row["farmer_id"])

  return 0


def _parse_pickup_date(raw: str) -> tuple[str | None, str]:
    s = str(raw or "").strip()
    if not s:
        return None, datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    import re

    m = re.match(r"^(\d{1,2})/(\d{1,2})/(\d{4})$", s)
    if m:
        d = f"{m.group(3)}-{int(m.group(2)):02d}-{int(m.group(1)):02d}"
        return d, f"{d} 09:00:00"
    m = re.match(r"^(\d{4})-(\d{2})-(\d{2})$", s)
    if m:
        return s, f"{s} 09:00:00"
    return None, datetime.now().strftime("%Y-%m-%d %H:%M:%S")


def _new_client_ref() -> str:
    import random

    return datetime.now().strftime("CW%Y%m%d%H%M%S") + f"{random.randint(0, 9999):04d}"


def _customer_tx_columns(cur) -> set[str]:
    cur.execute(
        """
        SELECT COLUMN_NAME FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_transaction'
        """
    )
    return {str(r.get("COLUMN_NAME") or "") for r in (cur.fetchall() or [])}


def _save_client_valid_id_file(tx_id: int, upload) -> tuple[Optional[str], Optional[str]]:
    if not upload or not getattr(upload, "filename", None):
        return None, None
    ext = "jpg"
    if "." in upload.filename:
        guess = upload.filename.rsplit(".", 1)[-1].lower()
        if guess in ("jpg", "jpeg", "png", "webp"):
            ext = "jpg" if guess == "jpeg" else guess
    os.makedirs(_CLIENT_ID_UPLOADS_DIR, exist_ok=True)
    fname = f"tx_{tx_id}_{int(datetime.now().timestamp())}.{ext}"
    path = os.path.join(_CLIENT_ID_UPLOADS_DIR, fname)
    upload.save(path)
    return f"/uploads/client_ids/{fname}", upload.filename


def handle_client_transaction_submit() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    if request.method != "POST":
        return _json_response({"ok": False, "error": "Method not allowed"}, 405)

    buyer = str(request.form.get("client_name") or request.form.get("buyer_name") or "").strip()
    product = str(request.form.get("product_type") or request.form.get("product") or "").strip()
    pickup_display = str(request.form.get("pickup_date") or "").strip()
    transaction_type = str(request.form.get("transaction_type") or "pickup").strip() or "pickup"
    payment_method = str(request.form.get("payment_method") or "Cash").strip() or "Cash"
    quantity_unit = str(request.form.get("quantity_unit") or "KG").strip() or "KG"

    if not buyer or not product:
        return _json_response({"ok": False, "error": "Name and product are required."}, 400)

    try:
        qty = float(str(request.form.get("quantity_kg") or "0"))
    except (TypeError, ValueError):
        qty = 0.0
    if qty <= 0:
        return _json_response({"ok": False, "error": "Quantity must be greater than zero."}, 400)

    try:
        amount = float(str(request.form.get("payment_amount") or "0"))
    except (TypeError, ValueError):
        amount = 0.0
    if amount <= 0:
        return _json_response({"ok": False, "error": "Amount to pay is required."}, 400)

    upload = request.files.get("valid_id")
    if not upload or not str(getattr(upload, "filename", "") or "").strip():
        return _json_response({"ok": False, "error": "Valid ID is required."}, 400)

    pickup_date, txn_date = _parse_pickup_date(pickup_display)
    ref = str(request.form.get("reference_no") or "").strip() or _new_client_ref()

    form_payload = {
        "transaction_type": transaction_type,
        "client_name": buyer,
        "pickup_date": pickup_display,
        "pickup_date_iso": pickup_date,
        "product_type": product,
        "quantity_kg": qty,
        "quantity_unit": quantity_unit,
        "payment_method": payment_method,
        "payment_amount": amount,
        "reference_no": ref,
        "submitted_from": "client_web",
    }
    form_json = json.dumps(form_payload, ensure_ascii=False)

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        farmer_id = _resolve_farmer_id_for_client_tx(cur, request.form)
        if farmer_id <= 0:
            return _json_response(
                {
                    "ok": False,
                    "error": "farmer_id is required. Open Transaction from the farmer profile (Go to Transaction).",
                },
                400,
            )

        cols = _customer_tx_columns(cur)
        base_row = {
            "farmer_id": farmer_id,
            "buyer_name": buyer,
            "product": product,
            "quantity": qty,
            "amount": amount,
            "payment_amount": amount,
            "payment_method": payment_method,
            "reference_no": ref,
            "transaction_date": txn_date,
        }
        optional = {
            "transaction_type": transaction_type,
            "pickup_date": pickup_date,
            "pickup_date_display": pickup_display,
            "quantity_unit": quantity_unit,
            "submitted_from": "client_web",
            "client_form_json": form_json,
        }

        cur.execute(
            "SELECT customer_transaction_id FROM customer_transaction WHERE reference_no = %s LIMIT 1",
            (ref,),
        )
        ex = cur.fetchone()
        row = {**base_row}
        for k, v in optional.items():
            if k in cols:
                row[k] = v

        if ex:
            tx_id = int(ex["customer_transaction_id"])
            sets = [f"{k} = %s" for k in row if k != "reference_no"]
            vals = [row[k] for k in row if k != "reference_no"] + [tx_id]
            cur.execute(
                f"UPDATE customer_transaction SET {', '.join(sets)} WHERE customer_transaction_id = %s",
                vals,
            )
        else:
            fields = list(row.keys())
            cur.execute(
                f"INSERT INTO customer_transaction ({', '.join(fields)}) VALUES ({', '.join(['%s'] * len(fields))})",
                [row[f] for f in fields],
            )
            tx_id = int(cur.lastrowid)

        valid_path, valid_name = _save_client_valid_id_file(tx_id, upload)
        if valid_path:
            form_payload["valid_id_path"] = valid_path
            form_payload["valid_id_filename"] = valid_name
            upd = {}
            if "valid_id_path" in cols:
                upd["valid_id_path"] = valid_path
            if "valid_id_filename" in cols and valid_name:
                upd["valid_id_filename"] = valid_name
            if "client_form_json" in cols:
                upd["client_form_json"] = json.dumps(form_payload, ensure_ascii=False)
            if upd:
                sets = [f"{k} = %s" for k in upd]
                cur.execute(
                    f"UPDATE customer_transaction SET {', '.join(sets)} WHERE customer_transaction_id = %s",
                    list(upd.values()) + [tx_id],
                )

        remarks = f"Client Web {transaction_type}"
        if pickup_display:
            remarks += f"; pickup={pickup_display}"
        if valid_path:
            remarks += f"; valid_id={valid_path}"

        cur.execute(
            """INSERT INTO transaction_history
               (customer_transaction_id, status, remarks, changed_by_user_id)
               VALUES (%s, %s, %s, NULL)""",
            (tx_id, "pending", remarks[:255]),
        )
        conn.commit()
        return _json_response(
            {
                "ok": True,
                "customer_transaction_id": tx_id,
                "reference_no": ref,
                "status": "pending",
                "farmer_id": farmer_id,
                "saved_fields": {**form_payload, "valid_id_saved": bool(valid_path)},
                "message": "Transaction submitted. Waiting for farmer approval in the app.",
            },
            200,
        )
    except Exception as e:
        if conn:
            try:
                conn.rollback()
            except Exception:
                pass
        return _json_response({"ok": False, "error": f"client_transaction_submit failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_farmer_pending_records() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    try:
        if request.method == "POST":
            body = request.get_json(silent=True) or {}
            user_id = int(body.get("user_id") or 0)
        else:
            user_id = int(request.args.get("user_id") or 0)
    except (TypeError, ValueError):
        user_id = 0
    if user_id <= 0:
        return _json_response({"ok": False, "error": "user_id is required."}, 400)

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        cur.execute("SELECT farmer_id FROM farmers WHERE user_id = %s LIMIT 1", (user_id,))
        farmer_row = cur.fetchone()
        if not farmer_row:
            return _json_response(
                {"ok": False, "error": "Farmer profile not found. Complete registration first."},
                404,
            )
        farmer_id = int(farmer_row.get("farmer_id") or 0)
        cur.execute(
            """
            SELECT ct.*
            FROM customer_transaction ct
            WHERE ct.farmer_id = %s
              AND (
              SELECT th.status
              FROM transaction_history th
              WHERE th.customer_transaction_id = ct.customer_transaction_id
              ORDER BY th.transaction_history_id DESC
              LIMIT 1
            ) = 'pending'
            ORDER BY ct.customer_transaction_id DESC
            LIMIT 200
            """,
            (farmer_id,),
        )
        rows = cur.fetchall() or []
        records = []
        for r in rows:
            qty = float(r.get("quantity") or 0)
            unit = str(r.get("quantity_unit") or "KG").strip() or "KG"
            pickup = str(r.get("pickup_date_display") or r.get("pickup_date") or "").strip()
            records.append(
                {
                    "id": f"tx-{int(r.get('customer_transaction_id') or 0)}",
                    "customer_transaction_id": int(r.get("customer_transaction_id") or 0),
                    "buyer": str(r.get("buyer_name") or "").strip(),
                    "product": str(r.get("product") or "").strip(),
                    "qty": str(int(qty)) if qty == int(qty) else str(qty),
                    "unit": unit,
                    "amount": float(r.get("amount") or 0),
                    "payment": str(r.get("payment_method") or "Cash").strip() or "Cash",
                    "paymentAmount": float(r.get("payment_amount") or 0),
                    "ref": str(r.get("reference_no") or "").strip(),
                    "status": "pending",
                    "at": str(r.get("transaction_date") or ""),
                    "transaction_type": str(r.get("transaction_type") or "pickup").strip(),
                    "pickup_date": pickup,
                    "valid_id_path": str(r.get("valid_id_path") or "").strip(),
                    "valid_id_filename": str(r.get("valid_id_filename") or "").strip(),
                    "submitted_from": str(r.get("submitted_from") or "").strip(),
                    "client_form_json": r.get("client_form_json"),
                }
            )
        return _json_response({"ok": True, "records": records, "count": len(records)}, 200)
    except Exception as e:
        return _json_response({"ok": False, "error": f"farmer_pending_records failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_farmer_record_action() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    body = request.get_json(silent=True) or {}
    try:
        user_id = int(body.get("user_id") or 0)
        tx_id = int(body.get("customer_transaction_id") or 0)
    except (TypeError, ValueError):
        user_id = 0
        tx_id = 0
    action = str(body.get("action") or "").strip().lower()
    if user_id <= 0:
        return _json_response({"ok": False, "error": "user_id is required."}, 400)
    if tx_id <= 0:
        return _json_response({"ok": False, "error": "customer_transaction_id is required."}, 400)
    if action not in ("approve", "dismiss"):
        return _json_response({"ok": False, "error": "action must be approve or dismiss."}, 400)

    status = "approved" if action == "approve" else "dismissed"
    remarks = "Approved by farmer in app" if action == "approve" else "Dismissed by farmer in app"

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        cur.execute("SELECT farmer_id FROM farmers WHERE user_id = %s LIMIT 1", (user_id,))
        farmer_row = cur.fetchone()
        if not farmer_row:
            return _json_response({"ok": False, "error": "Farmer profile not found."}, 404)
        farmer_id = int(farmer_row.get("farmer_id") or 0)
        cur.execute(
            """SELECT customer_transaction_id FROM customer_transaction
               WHERE customer_transaction_id = %s AND farmer_id = %s LIMIT 1""",
            (tx_id, farmer_id),
        )
        if not cur.fetchone():
            return _json_response(
                {"ok": False, "error": "Transaction not found for this farmer account."},
                404,
            )
        cur.execute(
            """INSERT INTO transaction_history
               (customer_transaction_id, status, remarks, changed_by_user_id)
               VALUES (%s, %s, %s, %s)""",
            (tx_id, status, remarks, user_id),
        )
        conn.commit()
        return _json_response(
            {"ok": True, "customer_transaction_id": tx_id, "status": status},
            200,
        )
    except Exception as e:
        if conn:
            try:
                conn.rollback()
            except Exception:
                pass
        return _json_response({"ok": False, "error": f"farmer_record_action failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_farmer_transaction_history() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    try:
        if request.method == "POST":
            body = request.get_json(silent=True) or {}
            user_id = int(body.get("user_id") or 0)
        else:
            user_id = int(request.args.get("user_id") or 0)
    except (TypeError, ValueError):
        user_id = 0
    if user_id <= 0:
        return _json_response({"ok": False, "error": "user_id is required."}, 400)

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        cur.execute("SELECT farmer_id FROM farmers WHERE user_id = %s LIMIT 1", (user_id,))
        farmer_row = cur.fetchone()
        if not farmer_row:
            return _json_response({"ok": False, "error": "Farmer profile not found."}, 404)
        farmer_id = int(farmer_row.get("farmer_id") or 0)
        cur.execute(
            """
            SELECT
              ct.customer_transaction_id,
              ct.buyer_name,
              ct.product,
              ct.quantity,
              ct.amount,
              ct.payment_amount,
              ct.payment_method,
              ct.reference_no,
              ct.transaction_date,
              (
                SELECT th.status
                FROM transaction_history th
                WHERE th.customer_transaction_id = ct.customer_transaction_id
                ORDER BY th.transaction_history_id DESC
                LIMIT 1
              ) AS current_status,
              (
                SELECT th.created_at
                FROM transaction_history th
                WHERE th.customer_transaction_id = ct.customer_transaction_id
                  AND th.status = 'approved'
                ORDER BY th.transaction_history_id ASC
                LIMIT 1
              ) AS approved_at
            FROM customer_transaction ct
            WHERE ct.farmer_id = %s
              AND (
              SELECT th.status
              FROM transaction_history th
              WHERE th.customer_transaction_id = ct.customer_transaction_id
              ORDER BY th.transaction_history_id DESC
              LIMIT 1
            ) IN ('approved', 'sent_to_client')
            ORDER BY COALESCE(approved_at, ct.transaction_date) DESC, ct.customer_transaction_id DESC
            LIMIT 300
            """,
            (farmer_id,),
        )
        rows = cur.fetchall() or []
        records = []
        for r in rows:
            qty = float(r.get("quantity") or 0)
            status = str(r.get("current_status") or "approved").strip().lower()
            at = r.get("approved_at") or r.get("transaction_date")
            records.append(
                {
                    "customer_transaction_id": int(r.get("customer_transaction_id") or 0),
                    "buyer": str(r.get("buyer_name") or "").strip(),
                    "product": str(r.get("product") or "").strip(),
                    "variety": str(r.get("product") or "").strip().lower(),
                    "qty": str(int(qty)) if qty == int(qty) else str(qty),
                    "unit": "KG",
                    "amount": float(r.get("amount") or 0),
                    "payment": str(r.get("payment_method") or "Cash").strip() or "Cash",
                    "paymentAmount": float(r.get("payment_amount") or 0),
                    "total": float(r.get("amount") or 0),
                    "change": max(
                        0, float(r.get("payment_amount") or 0) - float(r.get("amount") or 0)
                    ),
                    "ref": str(r.get("reference_no") or "").strip(),
                    "at": str(at) if at else "",
                    "sentToClient": status == "sent_to_client",
                    "sentAt": str(at) if status == "sent_to_client" and at else None,
                    "status": status,
                }
            )
        return _json_response({"ok": True, "records": records, "count": len(records)}, 200)
    except Exception as e:
        return _json_response({"ok": False, "error": f"farmer_transaction_history failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_client_transaction_status() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    ref = str(request.args.get("reference_no") or "").strip()
    try:
        tx_id = int(request.args.get("customer_transaction_id") or 0)
    except (TypeError, ValueError):
        tx_id = 0
    if not ref and tx_id <= 0:
        return _json_response(
            {"ok": False, "error": "reference_no or customer_transaction_id is required."},
            400,
        )

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        select_full = """SELECT customer_transaction_id, reference_no, buyer_name, product, quantity,
                          amount, payment_amount, payment_method, transaction_date,
                          pickup_date, pickup_date_display, quantity_unit
                   FROM customer_transaction WHERE """
        select_base = """SELECT customer_transaction_id, reference_no, buyer_name, product, quantity,
                          amount, payment_amount, payment_method, transaction_date
                   FROM customer_transaction WHERE """
        try:
            if tx_id > 0:
                cur.execute(select_full + "customer_transaction_id = %s LIMIT 1", (tx_id,))
            else:
                cur.execute(select_full + "reference_no = %s LIMIT 1", (ref,))
            row = cur.fetchone()
        except Exception:
            if tx_id > 0:
                cur.execute(select_base + "customer_transaction_id = %s LIMIT 1", (tx_id,))
            else:
                cur.execute(select_base + "reference_no = %s LIMIT 1", (ref,))
            row = cur.fetchone()
        if not row:
            return _json_response({"ok": False, "error": "Transaction not found."}, 404)
        cid = int(row["customer_transaction_id"])
        cur.execute(
            """SELECT status FROM transaction_history
               WHERE customer_transaction_id = %s
               ORDER BY transaction_history_id DESC LIMIT 1""",
            (cid,),
        )
        h = cur.fetchone()
        status = str((h or {}).get("status") or "pending").strip().lower()
        pickup_display = str(row.get("pickup_date_display") or "").strip()
        pickup_raw = str(row.get("pickup_date") or "")
        pickup_label = pickup_display or pickup_raw
        qty = float(row.get("quantity") or 0)
        pay_amt = float(row.get("payment_amount") or 0)
        amt = float(row.get("amount") or 0)
        total = amt if amt > 0 else (pay_amt if pay_amt > 0 else 0)
        change = max(0.0, pay_amt - total)
        unit = str(row.get("quantity_unit") or "KG").strip() or "KG"
        at = str(row.get("transaction_date") or "")
        payment_method = str(row.get("payment_method") or "").strip() or "Cash"
        ref_no = str(row.get("reference_no") or "")
        buyer_name = str(row.get("buyer_name") or "")
        product_name = str(row.get("product") or "")
        return _json_response(
            {
                "ok": True,
                "customer_transaction_id": cid,
                "reference_no": str(row.get("reference_no") or ""),
                "status": status,
                "is_pending": status == "pending",
                "is_approved": status == "approved",
                "is_dismissed": status == "dismissed",
                "is_sent_to_client": status == "sent_to_client",
                "buyer_name": str(row.get("buyer_name") or ""),
                "product": str(row.get("product") or ""),
                "quantity": qty,
                "quantity_kg": qty,
                "amount": amt,
                "payment_amount": pay_amt,
                "payment_method": str(row.get("payment_method") or ""),
                "pickup_date": pickup_label,
                "pickup_date_display": pickup_display,
                "quantity_unit": unit,
                "total": total,
                "change": change,
                "transaction_at": at,
                "receipt": {
                    "ref": ref_no,
                    "reference_no": ref_no,
                    "buyer": buyer_name,
                    "buyer_name": buyer_name,
                    "pickup_date": pickup_label,
                    "product": product_name,
                    "qty": qty,
                    "quantity_kg": qty,
                    "unit": unit,
                    "amount": total,
                    "payment": payment_method,
                    "payment_method": payment_method,
                    "paymentAmount": pay_amt,
                    "payment_amount": pay_amt,
                    "total": total,
                    "change": change,
                    "at": at,
                },
            },
            200,
        )
    except Exception as e:
        return _json_response({"ok": False, "error": f"client_transaction_status failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def _ensure_client_report_table(cur) -> None:
    cur.execute(
        """
        CREATE TABLE IF NOT EXISTS client_misconduct_report (
          report_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          reporter_name VARCHAR(255) NOT NULL,
          reporter_contact VARCHAR(255) NOT NULL DEFAULT '',
          reason_category VARCHAR(255) NOT NULL,
          reason_detail VARCHAR(255) NOT NULL DEFAULT '',
          allegation TEXT NOT NULL,
          chat_json TEXT NULL,
          farmer_id BIGINT UNSIGNED NULL,
          farmer_no VARCHAR(50) NULL,
          farmer_name VARCHAR(255) NOT NULL DEFAULT '',
          status VARCHAR(40) NOT NULL DEFAULT 'under review',
          INDEX idx_cmr_status (status),
          INDEX idx_cmr_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        """
    )


def handle_client_report_submit() -> Response:
    if request.method == "OPTIONS":
        return _preflight_ok()
    if request.method != "POST":
        return _json_response({"ok": False, "error": "Method not allowed"}, 405)

    body = request.get_json(silent=True) or {}
    reporter_name = str(body.get("reporter_name") or "").strip()
    if not reporter_name:
        return _json_response({"ok": False, "error": "reporter_name is required."}, 400)

    reason_category = str(body.get("reason_category") or body.get("reason") or "").strip()
    if not reason_category:
        return _json_response({"ok": False, "error": "reason_category is required."}, 400)

    reason_detail = str(body.get("reason_detail") or "").strip()
    allegation = str(body.get("allegation") or "").strip()
    if not allegation:
        return _json_response({"ok": False, "error": "allegation is required."}, 400)

    reporter_contact = str(body.get("reporter_contact") or "").strip()
    chat_json = body.get("chat_log") or body.get("chat_json")
    chat_str = None
    if chat_json is not None:
        chat_str = chat_json if isinstance(chat_json, str) else json.dumps(chat_json, ensure_ascii=False)

    farmer_id = int(body.get("farmer_id") or 0)
    farmer_no = str(body.get("farmer_no") or "").strip()
    farmer_name = str(body.get("farmer_name") or "").strip()

    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        _ensure_client_report_table(cur)

        if farmer_id > 0 and not farmer_name:
            cur.execute(
                """
                SELECT f.farmer_id, f.farm_code, u.username, pi.first_name, pi.last_name
                FROM farmers f
                LEFT JOIN users u ON u.user_id = f.user_id
                LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
                WHERE f.farmer_id = %s LIMIT 1
                """,
                (farmer_id,),
            )
            fr = cur.fetchone()
            if fr:
                fn = f"{fr.get('first_name') or ''} {fr.get('last_name') or ''}".strip()
                farmer_name = fn or str(fr.get("username") or "").strip()
                if not farmer_no and fr.get("farm_code"):
                    farmer_no = str(fr["farm_code"])
                elif not farmer_no:
                    farmer_no = str(farmer_id)

        cur.execute(
            """
            INSERT INTO client_misconduct_report
              (reporter_name, reporter_contact, reason_category, reason_detail, allegation, chat_json,
               farmer_id, farmer_no, farmer_name, status)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """,
            (
                reporter_name,
                reporter_contact,
                reason_category,
                reason_detail,
                allegation,
                chat_str,
                farmer_id if farmer_id > 0 else None,
                farmer_no or None,
                farmer_name,
                "under review",
            ),
        )
        conn.commit()
        report_id = int(cur.lastrowid or 0)
        return _json_response(
            {
                "ok": True,
                "report_id": report_id,
                "id": report_id,
                "message": "Your report was submitted. Our team will review it.",
            },
            200,
        )
    except Exception as e:
        return _json_response({"ok": False, "error": f"client_report_submit failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


_farmer_moderation_mod: Any = None


def _load_farmer_moderation():
    """Shared warning/suspend helpers from Beanthentic admin (optional)."""
    global _farmer_moderation_mod
    if _farmer_moderation_mod is not None:
        return _farmer_moderation_mod if _farmer_moderation_mod is not False else None
    root = os.path.abspath(os.path.join(_BASE_DIR, "..", "Beanthentic"))
    if os.path.isdir(root) and root not in sys.path:
        sys.path.insert(0, root)
    try:
        from config import farmer_moderation as fm

        _farmer_moderation_mod = fm
        return fm
    except Exception:
        _farmer_moderation_mod = False
        return None


def _ensure_farmer_mod_columns_inline(conn) -> None:
    cols = {
        "is_suspended": "TINYINT(1) NOT NULL DEFAULT 0",
        "suspended_until": "DATETIME NULL",
        "suspension_reason": "VARCHAR(500) NULL",
        "warning_count": "INT NOT NULL DEFAULT 0",
        "last_warning_at": "DATETIME NULL",
        "last_warning_reason": "VARCHAR(500) NULL",
    }
    with conn.cursor() as cur:
        for name, col_def in cols.items():
            cur.execute(
                """
                SELECT COUNT(*) AS c FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'farmers' AND COLUMN_NAME = %s
                """,
                (name,),
            )
            if int((cur.fetchone() or {}).get("c") or 0) == 0:
                cur.execute(f"ALTER TABLE farmers ADD COLUMN {name} {col_def}")


def _farmer_account_status_inline(conn, farmer_id: int) -> dict:
    fm = _load_farmer_moderation()
    if fm is not None:
        _ensure_farmer_mod_columns_inline(conn)
        return fm.farmer_account_status(conn, farmer_id)

    _ensure_farmer_mod_columns_inline(conn)
    with conn.cursor() as cur:
        cur.execute(
            """
            SELECT is_suspended, suspended_until, suspension_reason,
                   warning_count, last_warning_at, last_warning_reason
            FROM farmers WHERE farmer_id = %s LIMIT 1
            """,
            (farmer_id,),
        )
        row = cur.fetchone() or {}
    is_susp = int(row.get("is_suspended") or 0) == 1
    until_raw = row.get("suspended_until")
    active_susp = False
    if is_susp:
        if until_raw is None or until_raw == "":
            active_susp = True
        else:
            try:
                until_dt = (
                    until_raw
                    if isinstance(until_raw, datetime)
                    else datetime.strptime(str(until_raw)[:19], "%Y-%m-%d %H:%M:%S")
                )
                active_susp = until_dt > datetime.now()
            except (TypeError, ValueError):
                active_susp = True
    warned_at = row.get("last_warning_at")
    if warned_at and hasattr(warned_at, "strftime"):
        warned_at = warned_at.strftime("%Y-%m-%d %H:%M:%S")
    elif warned_at:
        warned_at = str(warned_at)[:19]
    return {
        "is_suspended": active_susp,
        "suspended_until": _format_mysql_datetime(until_raw) if until_raw and active_susp else None,
        "suspension_reason": str(row.get("suspension_reason") or ""),
        "warning_count": int(row.get("warning_count") or 0),
        "last_warning_reason": str(row.get("last_warning_reason") or ""),
        "last_warning_at": warned_at,
    }


def _lookup_user_farmer_row(conn, user_id: int, login_key: str) -> Optional[dict]:
    row = None
    if user_id > 0:
        with conn.cursor() as cur:
            cur.execute(
                """
                SELECT u.user_id, f.farmer_id
                FROM users u
                LEFT JOIN farmers f ON f.user_id = u.user_id
                WHERE u.user_id = %s
                LIMIT 1
                """,
                (user_id,),
            )
            row = cur.fetchone()
    if row or not login_key:
        return row
    parsed = parse_login_identifier(login_key)
    with conn.cursor() as cur:
        if parsed["type"] == "email" and parsed.get("email"):
            cur.execute(
                """
                SELECT u.user_id, f.farmer_id
                FROM users u
                LEFT JOIN farmers f ON f.user_id = u.user_id
                WHERE LOWER(TRIM(COALESCE(u.email, ''))) = %s AND u.is_active = 1
                LIMIT 1
                """,
                (parsed["email"].lower(),),
            )
        elif parsed.get("phone"):
            cur.execute(
                """
                SELECT u.user_id, f.farmer_id
                FROM users u
                LEFT JOIN farmers f ON f.user_id = u.user_id
                WHERE u.phone_number = %s AND u.is_active = 1
                LIMIT 1
                """,
                (parsed["phone"],),
            )
        else:
            return None
        return cur.fetchone()


def handle_farmer_account_status() -> Response:
    """GET — farmer app warning/suspend popup (mirrors api/farmer_account_status.php)."""
    if request.method == "OPTIONS":
        return _preflight_ok()
    if request.method != "GET":
        return _json_response({"ok": False, "error": "Method not allowed"}, 405)

    user_id = int(request.args.get("user_id") or 0)
    farmer_id_hint = int(request.args.get("farmer_id") or 0)
    login_key = str(
        request.args.get("login") or request.args.get("phone") or request.args.get("email") or ""
    ).strip()

    if user_id <= 0 and not login_key:
        return _json_response({"ok": False, "error": "user_id or login is required."}, 400)

    conn = None
    try:
        conn = _connect()
        row = _lookup_user_farmer_row(conn, user_id, login_key)
        if not row:
            return _json_response({"ok": False, "error": "User not found."}, 404)

        user_id = int(row.get("user_id") or 0)
        farmer_id = int(row.get("farmer_id") or 0)
        if farmer_id <= 0 and farmer_id_hint > 0:
            with conn.cursor() as cur:
                cur.execute(
                    """
                    SELECT f.farmer_id FROM farmers f
                    INNER JOIN users u ON u.user_id = f.user_id
                    WHERE f.farmer_id = %s AND u.user_id = %s
                    LIMIT 1
                    """,
                    (farmer_id_hint, user_id),
                )
                match = cur.fetchone()
                if match:
                    farmer_id = int(match["farmer_id"])

        account_warning = None
        account_suspended = None
        if farmer_id > 0:
            acct = _farmer_account_status_inline(conn, farmer_id)
            if acct.get("is_suspended"):
                account_suspended = {
                    "message": acct.get("suspension_reason")
                    or "Your account has been suspended by the administrator.",
                    "until": acct.get("suspended_until") or "",
                    "reason": acct.get("suspension_reason") or "",
                }
            wc = int(acct.get("warning_count") or 0)
            msg = str(acct.get("last_warning_reason") or "")
            if wc > 0 and msg:
                at = str(acct.get("last_warning_at") or "")
                account_warning = {
                    "message": msg,
                    "count": wc,
                    "at": at or None,
                    "token": f"{farmer_id}:{wc}:{at}:{hashlib.md5(msg.encode()).hexdigest()[:8]}",
                }

        return _json_response(
            {
                "ok": True,
                "user_id": user_id if user_id > 0 else None,
                "farmer_id": farmer_id if farmer_id > 0 else None,
                "account_warning": account_warning,
                "account_suspended": account_suspended,
            },
            200,
        )
    except Exception as e:
        return _json_response({"ok": False, "error": f"farmer_account_status failed: {e!s}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_registration_status() -> Response:
    """POST — resolve user_id / farmer_id by login (mirrors api/registration_status.php)."""
    if request.method == "OPTIONS":
        return _preflight_ok()
    if request.method != "POST":
        return _json_response({"ok": False, "error": "Method not allowed"}, 405)

    body = request.get_json(silent=True) or {}
    user_id = int(body.get("user_id") or 0)
    login_key = str(body.get("email") or body.get("phone_number") or body.get("login") or "").strip()
    if not login_key:
        return _json_response({"ok": False, "error": "Missing login identifier."}, 400)

    parsed = parse_login_identifier(login_key)
    if parsed["type"] == "empty":
        return _json_response({"ok": False, "error": "Invalid login identifier."}, 400)

    conn = None
    try:
        conn = _connect()
        row = _lookup_user_farmer_row(conn, user_id, login_key)
        if not row:
            return _json_response({"ok": False, "error": "Account not found."}, 404)

        farmer_id = int(row.get("farmer_id") or 0)
        resolved_user_id = int(row.get("user_id") or 0)
        status_raw = "pending"
        if farmer_id > 0:
            with conn.cursor() as cur:
                cur.execute(
                    "SELECT status FROM farmers WHERE farmer_id = %s LIMIT 1",
                    (farmer_id,),
                )
                fr = cur.fetchone() or {}
                status_raw = str(fr.get("status") or "pending").strip().lower()

        registered = farmer_id > 0 and status_raw == "active"
        from beanthentic_url_config import build_farmer_profile_url

        try:
            http_host = request.host
        except RuntimeError:
            http_host = ""
        profile_url = build_farmer_profile_url(http_host, farmer_id)

        return _json_response(
            {
                "ok": True,
                "registered": registered,
                "user_id": resolved_user_id if resolved_user_id > 0 else None,
                "farmer_id": farmer_id if farmer_id > 0 else None,
                "farmer_status": status_raw,
                "profile_url": profile_url,
            },
            200,
        )
    except Exception as e:
        return _json_response({"ok": False, "error": f"Registration status check failed: {e!s}"}, 503)
    finally:
        if conn:
            conn.close()


def register_mysql_json_routes(app) -> None:
    """Register /api/*.php routes that mirror PHP when Flask is the HTTP server."""

    @app.route("/api/farmer_account_status.php", methods=["GET", "OPTIONS"])
    def _farmer_account_status():
        return handle_farmer_account_status()

    @app.route("/api/registration_status.php", methods=["POST", "OPTIONS"])
    def _registration_status():
        return handle_registration_status()

    @app.route("/api/send_receipt.php", methods=["POST", "OPTIONS"])
    def _send_receipt():
        return handle_send_receipt()

    @app.route("/api/signup.php", methods=["POST", "OPTIONS"])
    def _signup():
        return handle_signup()

    @app.route("/api/login.php", methods=["POST", "OPTIONS"])
    def _login():
        return handle_login()

    @app.route("/api/session_verify.php", methods=["POST", "OPTIONS"])
    def _session_verify():
        return handle_session_verify()

    @app.route("/api/register_farm_farmer.php", methods=["POST", "OPTIONS"])
    def _register_farm_farmer():
        return handle_register_farm_farmer()

    @app.route("/api/chat_thread.php", methods=["GET", "POST", "OPTIONS"])
    def _chat_thread():
        return handle_chat_thread()

    @app.route("/api/chat_unread_count.php", methods=["GET", "OPTIONS"])
    def _chat_unread_count():
        return handle_chat_unread_count()

    @app.route("/api/admin_farmer_data.php", methods=["GET", "OPTIONS"])
    def _admin_farmer_data():
        return handle_admin_farmer_data()

    @app.route("/api/admin_customer_transactions.php", methods=["GET", "OPTIONS"])
    def _admin_customer_transactions():
        return handle_admin_customer_transactions()

    @app.route("/api/admin_client_reports.php", methods=["GET", "OPTIONS"])
    def _admin_client_reports():
        return handle_admin_client_reports()

    @app.route("/api/gi_updates.php", methods=["GET", "POST", "OPTIONS"])
    def _gi_updates():
        return handle_gi_updates(_connect, _json_response, _preflight_ok)

    @app.route("/api/admin_gi_contributions.php", methods=["GET", "PATCH", "DELETE", "OPTIONS"])
    def _admin_gi_contributions():
        return handle_admin_gi_contributions(_connect, _json_response, _preflight_ok)

    @app.route("/api/register-farm/farmer-profile", methods=["GET", "OPTIONS"])
    def _get_farmer_profile():
        return handle_get_farmer_profile()

    @app.route("/api/farmer_profile_update.php", methods=["POST", "OPTIONS"])
    def _farmer_profile_update():
        return handle_farmer_profile_update()
    
    @app.route("/api/client_farmers.php", methods=["GET", "OPTIONS"])
    def _client_farmers():
        return handle_client_farmers()

    @app.route("/api/client_profile_url.php", methods=["GET", "OPTIONS"])
    def _client_profile_url():
        return handle_client_profile_url()

    @app.route("/api/client_farmer_profile.php", methods=["GET", "OPTIONS"])
    def _client_farmer_profile():
        return handle_client_farmer_profile()

    @app.route("/api/client_transaction_submit.php", methods=["POST", "OPTIONS"])
    def _client_transaction_submit():
        return handle_client_transaction_submit()

    @app.route("/api/farmer_pending_records.php", methods=["GET", "POST", "OPTIONS"])
    def _farmer_pending_records():
        return handle_farmer_pending_records()

    @app.route("/api/farmer_record_action.php", methods=["POST", "OPTIONS"])
    def _farmer_record_action():
        return handle_farmer_record_action()

    @app.route("/api/client_transaction_status.php", methods=["GET", "OPTIONS"])
    def _client_transaction_status():
        return handle_client_transaction_status()

    @app.route("/api/client_report_submit.php", methods=["POST", "OPTIONS"])
    def _client_report_submit():
        return handle_client_report_submit()

    @app.route("/api/client_transaction_farmers.php", methods=["GET", "OPTIONS"])
    def _client_transaction_farmers():
        return handle_client_transaction_farmers()

    @app.route("/api/farmer_transaction_history.php", methods=["GET", "POST", "OPTIONS"])
    def _farmer_transaction_history():
        return handle_farmer_transaction_history()

