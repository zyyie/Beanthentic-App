from datetime import datetime
from flask import Flask, Response, request
import os
import re
import html
import sqlite3
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

@app.route("/about")
@app.route("/about.php")
def about_page():
    return _serve_php_asset("about.php")

@app.route("/mission-vision")
@app.route("/mission-vision.php")
def mission_vision_page():
    return _serve_php_asset("mission-vision.php")

@app.route("/how-to-get-there")
@app.route("/how-to-get-there.php")
def how_to_get_there_page():
    return _serve_php_asset("how-to-get-there.php")

@app.route("/profile.php")
def profile_page():
    farmer_id_raw = (request.args.get("farmer_id") or "").strip()
    name = (request.args.get("name") or "").strip()
    email = (request.args.get("email") or "").strip()

    farmer = None
    if farmer_id_raw.isdigit():
        try:
            conn = sqlite3.connect(os.path.join(BASE_DIR, "gi_database.db"))
            conn.row_factory = sqlite3.Row
            cur = conn.cursor()
            cur.execute(
                """
                SELECT id, name, email, phone, region, province, municipality, barangay, farm_address, farm_size
                FROM farmers
                WHERE id = ?
                """,
                (int(farmer_id_raw),),
            )
            row = cur.fetchone()
            conn.close()
            if row:
                farmer = dict(row)
        except Exception:
            farmer = None

    if farmer:
        name = str(farmer.get("name") or "").strip()
        email = str(farmer.get("email") or "").strip()
    else:
        farmer = {
            "id": farmer_id_raw if farmer_id_raw else "—",
            "name": name or "Member",
            "email": email or "—",
            "phone": "—",
            "region": "—",
            "province": "—",
            "municipality": "—",
            "barangay": "—",
            "farm_address": "—",
            "farm_size": "—",
        }

    if not name and email:
        name = email.split("@", 1)[0] if "@" in email else email
    if not name:
        name = "Member"

    parts = [p for p in re.split(r"\s+", name.strip()) if p]
    if len(parts) >= 2:
        initials = (parts[0][:1] + parts[-1][:1]).upper()
    elif len(parts) == 1:
        initials = (parts[0][:2]).upper()
    else:
        initials = "??"

    safe_initials = html.escape(initials, quote=True)
    safe_name = html.escape(name, quote=True)
    safe_email = html.escape(email if email else "—", quote=True)
    safe_id = html.escape(str(farmer.get("id", "—")), quote=True)
    safe_phone = html.escape(str(farmer.get("phone", "—")), quote=True)
    safe_region = html.escape(str(farmer.get("region", "—")), quote=True)
    safe_province = html.escape(str(farmer.get("province", "—")), quote=True)
    safe_municipality = html.escape(str(farmer.get("municipality", "—")), quote=True)
    safe_barangay = html.escape(str(farmer.get("barangay", "—")), quote=True)
    safe_farm_address = html.escape(str(farmer.get("farm_address", "—")), quote=True)
    farm_size = farmer.get("farm_size")
    if farm_size in (None, "", "—"):
        safe_farm_size = "—"
    else:
        try:
            safe_farm_size = f"{float(farm_size):.2f} ha"
        except Exception:
            safe_farm_size = html.escape(str(farm_size), quote=True)

    body = f"""<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
<meta name="theme-color" content="#25671E" />
<title>Profile · Beanthentic GI</title>
<link rel="stylesheet" href="/css/base.css">
<link rel="stylesheet" href="/css/layout.css">
<link rel="stylesheet" href="/css/components.css">
<link rel="stylesheet" href="/css/responsive.css">
<style>
  * {{ box-sizing: border-box; }}
  body {{
    margin: 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
    color: #111827;
  }}
  .header {{
    background: linear-gradient(135deg, #25671E 0%, #25671E 100%);
    color: #fff;
    padding: 2.2rem 0 1.8rem;
    text-align: center;
    position: relative;
  }}
  .header-back {{
    position: absolute;
    left: 16px;
    top: 18px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    background: rgba(255, 255, 255, 0.10);
    color: #ffffff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(6px);
    -webkit-tap-highlight-color: transparent;
  }}
  .header-back:hover {{ background: rgba(255, 255, 255, 0.16); }}
  .header-back:active {{ transform: scale(0.98); }}
  .header-back svg {{ width: 18px; height: 18px; }}
  @media (max-width: 480px) {{
    .header-back {{ left: 12px; top: 14px; padding: 9px 10px; }}
    .header-back span {{ display: none; }}
  }}
  .header h1 {{ margin: 0; font-size: 1.55rem; font-weight: 700; }}
  .header p {{ margin: .35rem 0 0; opacity: .92; font-size: .94rem; }}
  .main-content {{ padding: 1rem 0 1.8rem; }}
  .container {{ max-width: 1040px; margin: 0 auto; padding: 0 16px; }}
  .app-shell-intro {{ margin-bottom: .9rem; }}
  .callout-info {{
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border: 1px solid rgba(37, 103, 30, 0.2);
    border-radius: 12px;
    padding: .85rem 1rem;
    font-size: .88rem;
    color: #065f46;
    margin-bottom: .9rem;
    line-height: 1.45;
  }}
  .tabs {{
    display: flex;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    padding: .35rem;
    margin-bottom: .9rem;
  }}
  .tab {{
    width: 100%;
    min-height: 44px;
    border: none;
    border-radius: 8px;
    color: #fff;
    background: linear-gradient(135deg, #25671E 0%, #25671E 100%);
    font-size: .94rem;
    font-weight: 600;
  }}
  .card {{
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    padding: 1.35rem;
    margin-bottom: 1rem;
    border: 1px solid rgba(0,0,0,0.05);
  }}
  .card-header {{
    display: flex;
    align-items: center;
    gap: .8rem;
    margin-bottom: 1rem;
    padding-bottom: .8rem;
    border-bottom: 1px solid #e5e7eb;
  }}
  .card-icon {{
    width: 2.2rem;
    height: 2.2rem;
    border-radius: .6rem;
    background: #25671E;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
  }}
  .card-title {{ margin: 0; font-size: 1.25rem; font-weight: 700; }}
  .card-subtitle {{ margin: .1rem 0 0; color: #6b7280; font-size: .9rem; }}
  .badge {{
    display: inline-block;
    margin-top: .35rem;
    background: #ecfdf5;
    color: #166534;
    border: 1px solid rgba(22,101,52,.2);
    border-radius: 999px;
    padding: .22rem .56rem;
    font-size: .78rem;
    font-weight: 600;
  }}
  .form-grid {{
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .9rem;
  }}
  .form-group label {{
    display: block;
    margin-bottom: .32rem;
    font-size: .88rem;
    font-weight: 600;
    color: #374151;
  }}
  .form-group input {{
    width: 100%;
    padding: .72rem .78rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    color: #111827;
    font-size: .92rem;
  }}
  .form-group input[readonly] {{
    cursor: default;
  }}
  .form-group.full {{ grid-column: 1 / -1; }}
  @media (max-width: 900px) {{
    .form-grid {{ grid-template-columns: 1fr; }}
    .header {{ padding: 1.3rem 0 1.1rem; }}
  }}
</style>
</head>
<body>
  <header class="header">
    <a class="header-back" href="/account.php" id="profile-back-btn" aria-label="Back to Account">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M15 18l-6-6 6-6"></path>
      </svg>
      <span>Back to Account</span>
    </a>
    <h1>Register</h1>
    <p>Profile details for GI registration — view only.</p>
  </header>
  <main class="main-content">
    <div class="container">
      <div class="app-shell-intro">
        <div class="callout-info"><strong>Tip:</strong> This page mirrors the Register Farmer layout and shows your saved profile details in read-only mode.</div>
      </div>
      <div class="tabs" role="tablist" aria-label="Profile tabs">
        <button type="button" class="tab" aria-selected="true">Farmer Profile</button>
      </div>
      <section class="card">
        <div class="card-header">
          <div class="card-icon">{safe_initials}</div>
          <div>
            <h2 class="card-title">Farmer Registration</h2>
            <p class="card-subtitle">Complete registered information</p>
            <span class="badge">Farmer ID: {safe_id}</span>
          </div>
        </div>
        <div class="form-grid">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" value="{safe_name}" readonly>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="text" value="{safe_email}" readonly>
          </div>
          <div class="form-group">
            <label>Mobile Number</label>
            <input type="text" value="{safe_phone}" readonly>
          </div>
          <div class="form-group">
            <label>Region</label>
            <input type="text" value="{safe_region}" readonly>
          </div>
          <div class="form-group">
            <label>Province</label>
            <input type="text" value="{safe_province}" readonly>
          </div>
          <div class="form-group">
            <label>Municipality/City</label>
            <input type="text" value="{safe_municipality}" readonly>
          </div>
          <div class="form-group">
            <label>Barangay</label>
            <input type="text" value="{safe_barangay}" readonly>
          </div>
          <div class="form-group">
            <label>Farm Size (hectares)</label>
            <input type="text" value="{safe_farm_size}" readonly>
          </div>
          <div class="form-group full">
            <label>Complete Farm Address</label>
            <input type="text" value="{safe_farm_address}" readonly>
          </div>
        </div>
      </section>
    </div>
  </main>
  <script>
    (function () {{
      var btn = document.getElementById('profile-back-btn');
      if (!btn) return;
      btn.addEventListener('click', function (e) {{
        e.preventDefault();
        try {{
          window.location.assign('/account.php');
        }} catch (_e2) {{
          window.location.assign('account.php');
        }}
      }});
    }})();
  </script>
</body>
</html>"""
    return Response(body, mimetype="text/html; charset=utf-8")


if __name__ == "__main__":
    # Run on LAN so phones on same Wi‑Fi can reach it:
    # http://192.168.0.104:8000 (example)
    app.run(debug=True, host="0.0.0.0", port=8000)
