from datetime import datetime
from flask import Flask, Response
import os
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
    return Response(body, mimetype="text/html; charset=utf-8")


@app.route("/")
def home():
    return _serve_php_asset("index.php")


@app.route("/login.php")
def login_page():
    """Explicit route so Sign in always resolves (static_url_path '' can be flaky for .php)."""
    return _serve_php_asset("login.php")


@app.route("/signup.php")
def signup_page():
    return _serve_php_asset("signup.php")


if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)
