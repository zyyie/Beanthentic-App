from flask import Flask, send_from_directory
import os
from gi_module import GIModule
from maps_module import MapsModule

app = Flask(__name__, static_folder=".", static_url_path="")

# Initialize GI Module
gi_module = GIModule(app)

# Initialize Maps Module
maps_module = MapsModule(app)


@app.route("/")
def home():
    # Serve the existing index.html in the project root
    return send_from_directory(os.path.dirname(os.path.abspath(__file__)), "index.html")


if __name__ == "__main__":
    app.run(debug=True)

