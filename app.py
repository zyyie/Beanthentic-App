from datetime import datetime
from flask import Flask, Response, request
import os
import re
import html
from gi_module import GIModule
from maps_module import MapsModule

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
ASSETS_DIR = os.path.join(BASE_DIR, "android-app", "app", "src", "main", "assets")

app = Flask(__name__, static_folder=ASSETS_DIR, static_url_path="")

gi_module = GIModule(app)
maps_module = MapsModule(app)


def _serve_php_asset(filename: str) -> Response:
    path = os.path.join(ASSETS_DIR, filename)
    with open(path, encoding="utf-8") as f:
        body = f.read()
    body = body.replace("<?php echo date('Y'); ?>", str(datetime.now().year))
    # news.php: strip $title assignment and substitute echo (Flask is not PHP)
    title_decl = re.search(
        r"<\?php[\s\r\n]+\$title\s*=\s*'([^']*)'\s*;[\s\r\n]*\?>[\s\r\n]*",
        body,
    )
    if title_decl:
        title_text = title_decl.group(1)
        body = body.replace(title_decl.group(0), "", 1)
        body = body.replace("<?php echo $title; ?>", title_text)
    return Response(body, mimetype="text/html; charset=utf-8")


@app.route("/")
def home():
    return _serve_php_asset("index.php")


@app.route("/index.php")
def index_page():
    """Same as / but keeps hash links like index.php#home working over Flask."""
    return _serve_php_asset("index.php")


@app.route("/login.php")
def login_page():
    """Explicit route so Sign in always resolves (static_url_path '' can be flaky for .php)."""
    return _serve_php_asset("login.php")


@app.route("/account.php")
def account_page():
    return _serve_php_asset("account.php")


@app.route("/signup.php")
def signup_page():
    return _serve_php_asset("signup.php")


@app.route("/news.php")
def news_page():
    return _serve_php_asset("news.php")


@app.route("/privacy.php")
def privacy_page():
    return _serve_php_asset("privacy.php")


@app.route("/social.php")
def social_page():
    return _serve_php_asset("social.php")


@app.route("/settings.php")
def settings_page():
    return _serve_php_asset("settings.php")

@app.route("/profile.php")
def profile_page():
    """
    Public profile endpoint used by QR "data=".
    NOTE: Flask can't execute PHP, so we render the profile HTML here.
    """
    name = (request.args.get("name") or "").strip()
    email = (request.args.get("email") or "").strip()
    if not name and email:
        name = email.split("@", 1)[0] if "@" in email else email
    if not name:
        name = "Member"

    safe_name = html.escape(name, quote=True)
    safe_email = html.escape(email if email else "—", quote=True)

    parts = [p for p in re.split(r"\s+", name.strip()) if p]
    if len(parts) >= 2:
        initials = (parts[0][:1] + parts[-1][:1]).upper()
    elif len(parts) == 1:
        initials = (parts[0][:2]).upper()
    else:
        initials = "??"
    safe_initials = html.escape(initials, quote=True)

    body = f"""<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
<meta name="theme-color" content="#25671E" />
<title>Profile · Beanthentic</title>
<style>
  :root{{
    --bg1:#f4f7f4; --bg2:#faf8f5; --card:#ffffff;
    --green:#25671E; --green2:#2e8f4a; --text:#111827; --muted:#6b7280;
    --border: rgba(46,111,28,0.14);
  }}
  *{{box-sizing:border-box}}
  body{{
    margin:0;
    font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
    color:var(--text);
    background: linear-gradient(180deg, var(--bg1) 0%, var(--bg2) 40%, #fff 100%);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 18px max(16px, env(safe-area-inset-left)) calc(18px + env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-right));
  }}
  .wrap{{width:100%; max-width:520px;}}
  .card{{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:20px;
    overflow:hidden;
    box-shadow: 0 4px 24px rgba(22,38,28,0.06), 0 18px 48px rgba(46,111,28,0.10);
  }}
  .hero{{
    padding: 28px 20px 22px;
    text-align:center;
    color:#fdfaf6;
    background: linear-gradient(145deg, #1a5c34 0%, var(--green2) 45%, #3d7a32 100%);
    position:relative;
  }}
  .hero:after{{
    content:"";
    position:absolute; left:0; right:0; bottom:0; height:48px;
    background: linear-gradient(180deg, transparent, rgba(255,255,255,0.12));
    pointer-events:none;
  }}
  .avatar{{
    width:88px; height:88px;
    margin: 0 auto 12px;
    border-radius:999px;
    background: linear-gradient(135deg, rgba(253,250,246,0.95), rgba(227,245,232,0.92));
    border: 3px solid rgba(255,255,255,0.45);
    display:flex; align-items:center; justify-content:center;
    box-shadow: 0 6px 18px rgba(0,0,0,0.16);
    position:relative; z-index:1;
  }}
  .avatar span{{font-weight:800; letter-spacing:0.06em; color:#1f4d2e; font-size:26px;}}
  .kicker{{
    margin:0 0 6px;
    font-size:11px; font-weight:700;
    letter-spacing:0.18em; text-transform:uppercase;
    opacity:0.9; position:relative; z-index:1;
  }}
  h1{{
    margin:0 0 6px;
    font-size:24px;
    line-height:1.2;
    font-weight:800;
    position:relative; z-index:1;
    word-break: break-word;
  }}
  .email{{
    margin:0;
    font-size:14px;
    opacity:0.92;
    position:relative; z-index:1;
    word-break: break-word;
  }}
  .body{{padding: 18px 18px 20px;}}
  .label{{
    margin:0 0 8px;
    font-size:11px; font-weight:800; letter-spacing:0.14em; text-transform:uppercase;
    color:#6b7a66;
  }}
  .note{{
    margin:0 0 14px;
    color:#4a433c;
    font-size:14px;
    line-height:1.55;
  }}
  .pill{{
    display:inline-flex; align-items:center; gap:8px;
    padding: 10px 12px;
    border-radius: 14px;
    background: #fdfcfa;
    border: 1px solid rgba(198,157,125,0.28);
    width:100%;
  }}
  .pill strong{{
    display:block;
    font-size:12px;
    color:#8a7667;
    text-transform:uppercase;
    letter-spacing:0.08em;
  }}
  .pill span{{
    display:block;
    font-size:15px;
    font-weight:750;
    color:#2c241c;
    word-break: break-word;
  }}
  .row{{display:grid; gap:10px;}}
  .brand{{
    margin-top:14px;
    text-align:center;
    color: var(--muted);
    font-size:12px;
  }}
  .brand b{{color:var(--green)}}
</style></head><body>
<div class="wrap"><div class="card">
  <div class="hero">
    <div class="avatar"><span>{safe_initials}</span></div>
    <p class="kicker">Beanthentic profile</p>
    <h1>{safe_name}</h1>
    <p class="email">{safe_email}</p>
  </div>
  <div class="body">
    <p class="label">Shared via QR</p>
    <p class="note">This profile was opened from a QR code scan.</p>
    <div class="row">
      <div class="pill"><div><strong>Name</strong><span>{safe_name}</span></div></div>
      <div class="pill"><div><strong>Email</strong><span>{safe_email}</span></div></div>
    </div>
    <div class="brand">Powered by <b>Beanthentic</b></div>
  </div>
</div></div>
</body></html>"""
    return Response(body, mimetype="text/html; charset=utf-8")


if __name__ == "__main__":
    # Run on LAN so phones on same Wi‑Fi can reach it:
    # http://192.168.0.104:8000 (example)
    app.run(debug=True, host="0.0.0.0", port=8000)
