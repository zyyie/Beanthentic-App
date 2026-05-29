"""Resolve LAN-reachable Client Web base URLs (farmer QR codes)."""
from __future__ import annotations

import json
import os
import socket
from urllib.parse import urlparse

_BASE_DIR = os.path.dirname(os.path.abspath(__file__))
_DEFAULT_PORT = "5001"


def is_loopback_host(host: str) -> bool:
    h = (host or "").strip().lower()
    return h in ("localhost", "127.0.0.1", "::1", "[::1]", "0.0.0.0")


def guess_lan_ip() -> str:
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.settimeout(0.5)
        s.connect(("8.8.8.8", 80))
        ip = s.getsockname()[0]
        s.close()
        if ip and not ip.startswith("127."):
            return ip
    except OSError:
        pass
    return ""


def _configured_client_web_base() -> str:
    env = os.getenv("BEANTHENTIC_CLIENT_WEB_BASE", "").strip().rstrip("/")
    if env:
        return env
    try:
        cfg_path = os.path.join(_BASE_DIR, "..", "Beanthentic-Client-Web", "settings.json")
        if os.path.isfile(cfg_path):
            with open(cfg_path, encoding="utf-8") as f:
                raw = json.load(f)
            conn = raw.get("connection") if isinstance(raw, dict) else {}
            if isinstance(conn, dict):
                return str(conn.get("client_web_base") or "").strip().rstrip("/")
    except Exception:
        pass
    return ""


def resolve_client_web_base(http_host: str = "") -> str:
    """
    Base URL for Beanthentic-Client-Web (port 5001 by default).
    QR codes must use a phone-reachable host — not 127.0.0.1 when avoidable.
    """
    port = (os.getenv("BEANTHENTIC_CLIENT_WEB_PORT", _DEFAULT_PORT) or _DEFAULT_PORT).strip()
    browser_host = (http_host or "").split(":")[0].strip()

    configured = _configured_client_web_base()
    if configured:
        try:
            parsed = urlparse(configured)
            cfg_host = (parsed.hostname or "").strip()
            if browser_host and not is_loopback_host(browser_host) and is_loopback_host(cfg_host):
                return f"http://{browser_host}:{port}"
            if cfg_host and not is_loopback_host(cfg_host):
                return configured
        except Exception:
            if "127.0.0.1" not in configured and "localhost" not in configured.lower():
                return configured

    if browser_host and not is_loopback_host(browser_host):
        return f"http://{browser_host}:{port}"

    lan = guess_lan_ip()
    if lan:
        return f"http://{lan}:{port}"

    return f"http://127.0.0.1:{port}"


def build_farmer_profile_url(http_host: str = "", farmer_id: int = 0) -> str | None:
    """Short public profile URL for QR codes: {base}/farmer/{id}."""
    if farmer_id <= 0:
        return None
    return f"{resolve_client_web_base(http_host)}/farmer/{int(farmer_id)}"
