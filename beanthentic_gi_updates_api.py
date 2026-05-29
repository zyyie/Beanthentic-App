"""GI Updates / Farmer contributions — shared MySQL handlers for Flask (port 8080)."""
from __future__ import annotations

import json
import os
import re
import uuid
from datetime import datetime
from typing import Any, Optional

from flask import Response, request
from pymysql.err import OperationalError

_GI_UPLOADS_DIR = os.path.join(
    os.path.dirname(os.path.abspath(__file__)),
    "android-app",
    "app",
    "src",
    "main",
    "assets",
    "uploads",
    "gi_contributions",
)

_IMAGE_EXTS = {".jpg", ".jpeg", ".png", ".gif", ".webp"}
_DOC_EXTS = {".pdf", ".doc", ".docx"}


def _gi_app_server_base() -> str:
    base = os.environ.get("BEANTHENTIC_APP_SERVER_BASE", "").strip()
    if base:
        return base.rstrip("/")
    try:
        path = os.path.join(os.path.dirname(__file__), "..", "Beanthentic", "settings.json")
        if not os.path.isfile(path):
            path = os.path.join(os.path.dirname(__file__), "Beanthentic", "settings.json")
        if os.path.isfile(path):
            import json as _json

            raw = _json.loads(open(path, encoding="utf-8").read())
            conn = raw.get("connection") if isinstance(raw, dict) else {}
            if isinstance(conn, dict):
                return str(conn.get("app_server_base") or "").strip().rstrip("/")
    except Exception:
        pass
    return ""


def ensure_gi_updates_table(cur) -> None:
    cur.execute(
        """
        CREATE TABLE IF NOT EXISTS gi_updates (
          gi_update_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          farmer_id BIGINT UNSIGNED NULL,
          title VARCHAR(150) NOT NULL,
          content TEXT NOT NULL,
          image_url VARCHAR(255) NULL,
          attachments_json TEXT NULL,
          upload_status ENUM('pending','approved','archived','rejected') NOT NULL DEFAULT 'pending',
          is_starred TINYINT(1) NOT NULL DEFAULT 0,
          is_read_admin TINYINT(1) NOT NULL DEFAULT 0,
          category VARCHAR(30) NOT NULL DEFAULT 'general',
          sender_name VARCHAR(255) NULL,
          progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
          current_phase VARCHAR(100) NULL,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX idx_gi_updates_farmer (farmer_id),
          INDEX idx_gi_updates_status (upload_status, is_read_admin),
          INDEX idx_gi_updates_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        """
    )
    migrations = [
        ("attachments_json", "TEXT NULL"),
        (
            "upload_status",
            "ENUM('pending','approved','archived','rejected') NOT NULL DEFAULT 'pending'",
        ),
        ("is_starred", "TINYINT(1) NOT NULL DEFAULT 0"),
        ("is_read_admin", "TINYINT(1) NOT NULL DEFAULT 0"),
        ("category", "VARCHAR(30) NOT NULL DEFAULT 'general'"),
        ("sender_name", "VARCHAR(255) NULL"),
    ]
    for col, ddl in migrations:
        try:
            cur.execute(f"ALTER TABLE gi_updates ADD COLUMN {col} {ddl}")
        except OperationalError as e:
            if e.args and e.args[0] != 1060:
                raise


def _gi_title_from_content(content: str) -> str:
    text = re.sub(r"\s+", " ", (content or "").strip())
    if not text:
        return "GI Update"
    return text[:147] + ("..." if len(text) > 150 else "")


def _gi_category_from_names(names: list[str]) -> str:
    has_image = False
    has_doc = False
    for name in names:
        ext = os.path.splitext(str(name or "").lower())[1]
        if ext in _IMAGE_EXTS:
            has_image = True
        elif ext in _DOC_EXTS or ext:
            has_doc = True
    if has_image and not has_doc:
        return "images"
    if has_doc:
        return "documents"
    return "general"


def _gi_parse_attachments(raw: Any) -> list[dict]:
    if not raw:
        return []
    if isinstance(raw, list):
        return raw
    try:
        data = json.loads(str(raw))
        return data if isinstance(data, list) else []
    except Exception:
        return []


def _gi_attachments_with_urls(attachments: list[dict], base: str) -> list[dict]:
    out = []
    for a in attachments:
        if not isinstance(a, dict):
            continue
        path = str(a.get("path") or "").strip()
        url = path
        if path.startswith("/") and base:
            url = base + path
        out.append({**a, "url": url})
    return out


def _gi_farmer_display_name(row: dict) -> str:
    fn = str(row.get("first_name") or "").strip()
    ln = str(row.get("last_name") or "").strip()
    name = (fn + " " + ln).strip()
    if name:
        return name
    return str(row.get("username") or row.get("phone_number") or "Farmer").strip() or "Farmer"


def _gi_resolve_farmer(cur, user_id: int) -> Optional[dict]:
    if user_id <= 0:
        return None
    cur.execute(
        """
        SELECT f.farmer_id, u.user_id, u.username, u.phone_number, u.email,
               pi.first_name, pi.last_name
        FROM users u
        LEFT JOIN farmers f ON f.user_id = u.user_id
        LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
        WHERE u.user_id = %s
        LIMIT 1
        """,
        (user_id,),
    )
    row = cur.fetchone()
    if not row or not row.get("farmer_id"):
        return None
    return row


def _gi_save_upload_files(farmer_id: int, update_id: int, files) -> list[dict]:
    saved: list[dict] = []
    if not files:
        return saved
    os.makedirs(_GI_UPLOADS_DIR, exist_ok=True)
    for f in files:
        if not f or not getattr(f, "filename", None):
            continue
        orig = str(f.filename or "file").strip()
        ext = os.path.splitext(orig)[1].lower() or ".bin"
        if ext not in _IMAGE_EXTS | _DOC_EXTS | {".bin"}:
            continue
        safe = re.sub(r"[^a-zA-Z0-9._-]+", "_", orig)[:120]
        fname = f"gi_{farmer_id}_{update_id}_{uuid.uuid4().hex[:10]}{ext}"
        path = os.path.join(_GI_UPLOADS_DIR, fname)
        try:
            f.save(path)
        except Exception:
            continue
        mime = str(getattr(f, "mimetype", "") or "")
        saved.append(
            {
                "name": safe,
                "path": f"/uploads/gi_contributions/{fname}",
                "mime": mime,
                "size": os.path.getsize(path) if os.path.isfile(path) else 0,
            }
        )
    return saved


def _gi_row_to_admin_item(row: dict, base: str) -> dict:
    content = str(row.get("content") or "")
    preview = content.replace("\n", " ").strip()
    if len(preview) > 160:
        preview = preview[:157] + "..."
    attachments = _gi_attachments_with_urls(_gi_parse_attachments(row.get("attachments_json")), base)
    at = row.get("created_at")
    return {
        "gi_update_id": int(row.get("gi_update_id") or 0),
        "id": int(row.get("gi_update_id") or 0),
        "farmer_id": int(row.get("farmer_id") or 0),
        "farmer_name": str(row.get("farmer_name") or row.get("sender_name") or "Farmer"),
        "farmer_email": str(row.get("email") or row.get("username") or "").strip(),
        "title": str(row.get("title") or ""),
        "subject": str(row.get("title") or ""),
        "content": content,
        "preview": preview,
        "upload_status": str(row.get("upload_status") or "pending"),
        "status": str(row.get("upload_status") or "pending"),
        "category": str(row.get("category") or "general"),
        "is_starred": bool(int(row.get("is_starred") or 0)),
        "starred": bool(int(row.get("is_starred") or 0)),
        "is_read_admin": bool(int(row.get("is_read_admin") or 0)),
        "unread": not bool(int(row.get("is_read_admin") or 0)),
        "current_phase": str(row.get("current_phase") or ""),
        "progress_percent": float(row.get("progress_percent") or 0),
        "attachments": attachments,
        "created_at": at.isoformat() if hasattr(at, "isoformat") else str(at or ""),
    }


def handle_gi_updates(_connect, _json_response, _preflight_ok) -> Response:
    """Farmer GI Updates page — progress + submit."""
    if request.method == "OPTIONS":
        return _preflight_ok()
    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        ensure_gi_updates_table(cur)
        base = _gi_app_server_base()

        if request.method == "GET":
            user_id = int(request.args.get("user_id") or 0)
            farmer_row = _gi_resolve_farmer(cur, user_id)
            if not farmer_row:
                return _json_response({"ok": False, "error": "Farmer account not found."}, 404)
            farmer_id = int(farmer_row["farmer_id"])
            cur.execute(
                """
                SELECT COALESCE(MAX(progress_percent), 0) AS progress_percent
                FROM gi_updates
                WHERE farmer_id = %s AND current_phase = 'admin_progress'
                """,
                (farmer_id,),
            )
            prog = cur.fetchone() or {}
            progress = float(prog.get("progress_percent") or 0)
            cur.execute(
                """
                SELECT gi_update_id, title, content, attachments_json, upload_status,
                       category, created_at, current_phase
                FROM gi_updates
                WHERE farmer_id = %s AND current_phase = 'farmer_submission'
                ORDER BY created_at DESC, gi_update_id DESC
                LIMIT 50
                """,
                (farmer_id,),
            )
            submissions = [
                _gi_row_to_admin_item(
                    {**r, "farmer_name": _gi_farmer_display_name(farmer_row), "email": farmer_row.get("email")},
                    base,
                )
                for r in (cur.fetchall() or [])
            ]
            return _json_response(
                {
                    "ok": True,
                    "progress_percent": progress,
                    "submissions": submissions,
                    "farmer_name": _gi_farmer_display_name(farmer_row),
                },
                200,
            )

        if request.method != "POST":
            return _json_response({"ok": False, "error": "Method not allowed."}, 405)

        user_id = int(request.form.get("user_id") or request.args.get("user_id") or 0)
        if user_id <= 0:
            body = request.get_json(silent=True) or {}
            if isinstance(body, dict):
                user_id = int(body.get("user_id") or 0)
        farmer_row = _gi_resolve_farmer(cur, user_id)
        if not farmer_row:
            return _json_response({"ok": False, "error": "Farmer account not found."}, 404)
        farmer_id = int(farmer_row["farmer_id"])
        content = str(request.form.get("message") or request.form.get("content") or "").strip()
        if not content:
            body = request.get_json(silent=True) or {}
            if isinstance(body, dict):
                content = str(body.get("message") or body.get("content") or "").strip()
        files = []
        if request.files:
            files.extend(request.files.getlist("files"))
            files.extend(request.files.getlist("gi_support_file"))
        if not content and not files:
            return _json_response({"ok": False, "error": "Type a message or attach at least one file."}, 400)

        sender_name = _gi_farmer_display_name(farmer_row)
        title = _gi_title_from_content(content)
        category = _gi_category_from_names([getattr(f, "filename", "") for f in files])

        cur.execute(
            """
            INSERT INTO gi_updates
              (farmer_id, title, content, upload_status, is_read_admin, category,
               sender_name, current_phase, progress_percent)
            VALUES (%s, %s, %s, 'pending', 0, %s, %s, 'farmer_submission', 0)
            """,
            (farmer_id, title, content or "(attachments only)", category, sender_name),
        )
        update_id = int(cur.lastrowid or 0)
        attachments = _gi_save_upload_files(farmer_id, update_id, files)
        if attachments:
            cur.execute(
                "UPDATE gi_updates SET attachments_json = %s WHERE gi_update_id = %s",
                (json.dumps(attachments), update_id),
            )
        conn.commit()
        return _json_response(
            {
                "ok": True,
                "gi_update_id": update_id,
                "message": "GI update sent to admin for review.",
            },
            200,
        )
    except Exception as e:
        if conn:
            try:
                conn.rollback()
            except Exception:
                pass
        return _json_response({"ok": False, "error": f"gi_updates failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()


def handle_admin_gi_contributions(_connect, _json_response, _preflight_ok) -> Response:
    """Admin Farmer's Contribution inbox."""
    if request.method == "OPTIONS":
        return _preflight_ok()
    conn = None
    try:
        conn = _connect()
        cur = conn.cursor()
        ensure_gi_updates_table(cur)
        base = _gi_app_server_base()

        if request.method == "GET":
            try:
                limit = int(request.args.get("limit") or 500)
            except (TypeError, ValueError):
                limit = 500
            limit = max(1, min(limit, 800))
            cur.execute(
                f"""
                SELECT g.*, u.email, u.username, u.phone_number,
                       pi.first_name, pi.last_name
                FROM gi_updates g
                LEFT JOIN farmers f ON f.farmer_id = g.farmer_id
                LEFT JOIN users u ON u.user_id = f.user_id
                LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
                WHERE g.current_phase = 'farmer_submission'
                ORDER BY g.created_at DESC, g.gi_update_id DESC
                LIMIT {int(limit)}
                """
            )
            items = []
            for row in cur.fetchall() or []:
                fn = str(row.get("first_name") or "").strip()
                ln = str(row.get("last_name") or "").strip()
                farmer_name = (fn + " " + ln).strip() or str(row.get("sender_name") or "Farmer")
                items.append(_gi_row_to_admin_item({**row, "farmer_name": farmer_name}, base))
            return _json_response({"ok": True, "items": items, "count": len(items)}, 200)

        if request.method == "PATCH":
            body = request.get_json(silent=True) or {}
            if not isinstance(body, dict):
                return _json_response({"ok": False, "error": "Invalid JSON body."}, 400)

            action = str(body.get("action") or "").strip().lower()
            if action == "set_progress":
                farmer_id = int(body.get("farmer_id") or 0)
                progress = float(body.get("progress_percent") or 0)
                if progress < 0:
                    progress = 0
                if progress > 100:
                    progress = 100
                note = str(body.get("note") or body.get("content") or "").strip()
                if farmer_id <= 0:
                    return _json_response({"ok": False, "error": "farmer_id required."}, 400)
                title = "GI Progress Update"
                cur.execute(
                    """
                    INSERT INTO gi_updates
                      (farmer_id, title, content, upload_status, is_read_admin,
                       category, current_phase, progress_percent)
                    VALUES (%s, %s, %s, 'approved', 1, 'general', 'admin_progress', %s)
                    """,
                    (farmer_id, title, note or f"Progress set to {progress:.0f}%", progress),
                )
                conn.commit()
                return _json_response({"ok": True, "progress_percent": progress}, 200)

            gi_id = int(body.get("gi_update_id") or body.get("id") or 0)
            if gi_id <= 0:
                return _json_response({"ok": False, "error": "gi_update_id required."}, 400)

            sets = []
            args: list[Any] = []
            if "is_starred" in body or "starred" in body:
                sets.append("is_starred = %s")
                args.append(1 if body.get("is_starred") or body.get("starred") else 0)
            if "is_read_admin" in body or "unread" in body:
                val = body.get("is_read_admin")
                if val is None and "unread" in body:
                    val = 0 if body.get("unread") else 1
                sets.append("is_read_admin = %s")
                args.append(1 if val else 0)
            if "upload_status" in body or "status" in body:
                status = str(body.get("upload_status") or body.get("status") or "pending").strip().lower()
                if status not in ("pending", "approved", "archived", "rejected"):
                    status = "pending"
                sets.append("upload_status = %s")
                args.append(status)
            if not sets:
                return _json_response({"ok": False, "error": "No fields to update."}, 400)
            args.append(gi_id)
            cur.execute(
                f"UPDATE gi_updates SET {', '.join(sets)} WHERE gi_update_id = %s AND current_phase = 'farmer_submission'",
                tuple(args),
            )
            conn.commit()
            return _json_response({"ok": True, "updated": cur.rowcount}, 200)

        if request.method == "DELETE":
            gi_id = int(request.args.get("gi_update_id") or request.args.get("id") or 0)
            if gi_id <= 0:
                body = request.get_json(silent=True) or {}
                if isinstance(body, dict):
                    gi_id = int(body.get("gi_update_id") or body.get("id") or 0)
            if gi_id <= 0:
                return _json_response({"ok": False, "error": "gi_update_id required."}, 400)
            cur.execute(
                "DELETE FROM gi_updates WHERE gi_update_id = %s AND current_phase = 'farmer_submission'",
                (gi_id,),
            )
            conn.commit()
            return _json_response({"ok": True, "deleted": cur.rowcount}, 200)

        return _json_response({"ok": False, "error": "Method not allowed."}, 405)
    except Exception as e:
        if conn:
            try:
                conn.rollback()
            except Exception:
                pass
        return _json_response({"ok": False, "error": f"admin_gi_contributions failed: {e}"}, 500)
    finally:
        if conn:
            conn.close()
