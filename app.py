from datetime import datetime
from flask import Flask, Response
import os
import re
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


if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)
