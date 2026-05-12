from flask import request, jsonify, render_template_string
import sqlite3
import json
import os
import re
import shutil

EMAIL_RE = re.compile(r"^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$")
MODULE_DIR = os.path.dirname(os.path.abspath(__file__))
ANDROID_APP_DIR = os.path.abspath(os.path.join(MODULE_DIR, "..", "..", "..", ".."))
PROJECT_ROOT = os.path.abspath(os.path.join(ANDROID_APP_DIR, ".."))
REGISTER_DB_PATH = os.path.join(ANDROID_APP_DIR, "register_farm_database.db")
ROOT_REGISTER_DB_COMPAT_PATH = os.path.join(PROJECT_ROOT, "register_farm_database.db")
LEGACY_DB_NAME = "g" + "i_database.db"
LEGACY_DB_PATHS = [
    os.path.join(ANDROID_APP_DIR, LEGACY_DB_NAME),
    os.path.join(PROJECT_ROOT, LEGACY_DB_NAME),
]


def _truthy(val):
    return val in (True, "yes", "on", "1", 1)

PH_REGIONS = frozenset({
    "Ilocos Region", "Cagayan Valley", "Central Luzon", "CALABARZON", "MIMAROPA",
    "Bicol Region", "Western Visayas", "Central Visayas", "Eastern Visayas",
    "Zamboanga Peninsula", "Northern Mindanao", "Davao Region", "SOCCSKSARGEN",
    "CARAGA", "CAR", "NCR",
})


def _normalize_ph_phone(raw):
    if raw is None:
        return ""
    s = re.sub(r"[\s\-]", "", str(raw).strip())
    if not s:
        return ""
    if s.startswith("+63"):
        s = "0" + s[3:]
    elif s.startswith("63") and len(s) >= 11:
        s = "0" + s[2:]
    return s


_LIPA_BARANGAYS = frozenset({
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
})


def _float_non_neg(val, errors, key, max_val=100_000):
    if val in (None, ""):
        return None
    try:
        n = float(val)
    except (TypeError, ValueError):
        errors[key] = "Must be a valid number."
        return None
    if n < 0 or n > max_val:
        errors[key] = f"Must be between 0 and {max_val:,.0f}."
        return None
    return n


def _int_non_neg(val, errors, key, max_val=999_999_999):
    if val in (None, ""):
        return 0
    try:
        n = int(float(val))
    except (TypeError, ValueError):
        errors[key] = "Whole number only."
        return None
    if n < 0 or n > max_val:
        errors[key] = "Enter a sensible count."
        return None
    return n


def _plant_area_to_hectares(amount, unit):
    if unit == "sqm":
        return amount / 10_000.0
    if unit == "ac":
        return amount * 0.40468564224
    return amount


def _sanitize_profile_photo_data(data):
    """Persist registration photo as a data URL in SQLite (bounded size)."""
    if not isinstance(data, dict):
        return ""
    raw = (data.get("profile_photo_data") or "").strip()
    if not raw:
        return ""
    if len(raw) > 2_500_000:
        raw = raw[:2_500_000]
    if raw.startswith("data:image/"):
        return raw
    return ""


def validate_farmer_payload(data):
    errors = {}
    if not isinstance(data, dict):
        return None, {"_error": "Request body must be JSON."}

    region = "CALABARZON"
    province = "Batangas"
    municipality = "Lipa City"
    barangay = (data.get("barangay") or "").strip()
    email = (data.get("email") or "").strip().lower()
    phone = _normalize_ph_phone(data.get("phone"))

    # --- Legacy single-page registration (farm_house + farm_landmark) ---
    legacy_house = (data.get("farm_house") or "").strip()
    legacy_landmark = (data.get("farm_landmark") or "").strip()
    is_legacy_body = legacy_house and legacy_landmark and not (
        (data.get("last_name") or "").strip()
        or (data.get("first_name") or "").strip()
        or (data.get("ownership_status") or "").strip()
    )

    if is_legacy_body:
        name = (data.get("name") or "").strip()
        farm_address = (data.get("farm_address") or "").strip()
        if not farm_address:
            farm_address = f"{legacy_house}. Landmark: {legacy_landmark}"
        farm_size_raw = data.get("farm_size", 0)

        if len(name) < 3:
            errors["name"] = "Enter your full legal name (at least 3 characters)."
        elif len(name) > 120:
            errors["name"] = "Name must be at most 120 characters."
        elif not any(ch.isalpha() for ch in name):
            errors["name"] = "Name must include letters (not numbers-only or symbols)."
        if not EMAIL_RE.match(email):
            errors["email"] = "Enter a valid email address."
        if not phone:
            errors["phone"] = "Mobile number is required (09XXXXXXXXX)."
        elif not re.match(r"^09\d{9}$", phone):
            errors["phone"] = "Use Philippine mobile format: 09XXXXXXXXX (11 digits)."

        if len(farm_address) < 20 or len(farm_address) > 500:
            errors["farm_address"] = "Enter the complete farm address (at least 20 characters)."

        farm_size = 0.0
        if farm_size_raw not in (None, ""):
            try:
                farm_size = float(farm_size_raw)
            except (TypeError, ValueError):
                errors["farm_size"] = "Farm size must be a valid number."
            else:
                if farm_size < 0 or farm_size > 100_000:
                    errors["farm_size"] = "Farm size must be between 0 and 100,000 hectares."

        if not barangay:
            errors["barangay"] = "Select your barangay in Lipa City."
        elif barangay not in _LIPA_BARANGAYS:
            errors["barangay"] = "Barangay must be within Lipa City."

        if not _truthy(data.get("agree_registration")):
            errors["agree_registration"] = "Confirm that your registration details are true and complete."

        if errors:
            return None, errors

        return {
            "name": name,
            "email": email,
            "phone": phone,
            "region": region,
            "province": province,
            "municipality": municipality,
            "barangay": barangay,
            "farm_address": farm_address,
            "farm_size": float(farm_size),
            "first_name": "",
            "last_name": "",
            "federation": "",
            "association": "",
            "rsbsa_registered": "",
            "rsbsa_number": "",
            "ownership_status": "",
            "plant_area_value": None,
            "plant_area_unit": "",
            "trees_json": "{}",
            "production_json": "{}",
            "profile_photo_data": "",
        }, {}

    last_name = (data.get("last_name") or "").strip()
    first_name = (data.get("first_name") or "").strip()
    name = (data.get("name") or "").strip()
    if last_name or first_name:
        if len(last_name) < 2:
            errors["last_name"] = "Enter your family name."
        elif len(last_name) > 80:
            errors["last_name"] = "Last name is too long."
        if len(first_name) < 2:
            errors["first_name"] = "Enter your given name."
        elif len(first_name) > 80:
            errors["first_name"] = "First name is too long."
        name = f"{last_name}, {first_name}".strip(", ")
    else:
        if len(name) < 3:
            errors["last_name"] = "Enter Last name and First name."
        elif len(name) > 160:
            errors["name"] = "Name must be at most 160 characters."

    if not EMAIL_RE.match(email):
        errors["email"] = "Enter a valid email address."
    if not phone:
        errors["phone"] = "Mobile number is required (09XXXXXXXXX)."
    elif not re.match(r"^09\d{9}$", phone):
        errors["phone"] = "Use Philippine mobile format: 09XXXXXXXXX."

    if not barangay:
        errors["barangay"] = "Select your barangay in Lipa City."
    elif barangay not in _LIPA_BARANGAYS:
        errors["barangay"] = "Barangay must be within Lipa City."

    affiliation_role = (data.get("affiliation_role") or "").strip()
    if affiliation_role not in ("Cluster Head", "Officer", "Member"):
        errors["affiliation_role"] = "Select your role (Cluster Head, Officer, or Member)."

    ncfrs = (data.get("ncfrs") or "").strip().lower()
    if ncfrs not in ("yes", "no"):
        errors["ncfrs"] = "Select NCFRS (Yes or No)."

    # Keep legacy columns populated for existing DB schema expectations.
    federation = affiliation_role
    association = ""

    rsbsa_registered = (data.get("rsbsa_registered") or "").strip().lower()
    if rsbsa_registered not in ("yes", "no", "pending"):
        errors["rsbsa_registered"] = "Select RSBSA registration status."
    rsbsa_number = (data.get("rsbsa_number") or "").strip()
    if rsbsa_registered == "yes":
        if len(rsbsa_number) < 4 or len(rsbsa_number) > 120:
            errors["rsbsa_number"] = "Enter a valid RSBSA number."
    elif len(rsbsa_number) > 120:
        errors["rsbsa_number"] = "RSBSA number is too long."

    ownership_status = (data.get("ownership_status") or "").strip().lower()
    allowed_own = frozenset({"landowner", "cloa_holder", "list_holder", "sessional_farm_worker", "others"})
    if ownership_status not in allowed_own:
        errors["ownership_status"] = "Select status of ownership."

    plant_area_unit = (data.get("plant_area_unit") or "").strip().lower()
    if plant_area_unit not in ("ha", "sqm", "ac"):
        errors["plant_area_unit"] = "Select an area unit (ha, sqm, or acre)."

    if data.get("plant_area_value") in (None, ""):
        errors["plant_area_value"] = "Enter total plant area."
        plant_area_value = None
    else:
        plant_area_value = _float_non_neg(data.get("plant_area_value"), errors, "plant_area_value", max_val=1_000_000)

    if plant_area_value is not None and plant_area_value <= 0:
        errors["plant_area_value"] = "Total plant area must be greater than zero."

    trees = {}
    varieties = ["liberica", "robusta", "excelsa"]
    for v in varieties:
        b_key = f"{v}_bearing"
        n_key = f"{v}_non_bearing"
        tb = data.get(b_key)
        tn = data.get(n_key)
        bi = _int_non_neg(tb, errors, b_key)
        ni = _int_non_neg(tn, errors, n_key)
        if bi is None or ni is None:
            trees[v] = None
        else:
            trees[v] = {"bearing": bi, "non_bearing": ni}

    prod_units_allowed = frozenset({"kg", "sacks", "tons"})
    production = {}
    for v in varieties:
        qty_key = f"{v}_prod_qty"
        unit_key = f"{v}_prod_unit"
        raw_q = data.get(qty_key)
        if raw_q in (None, ""):
            q = 0.0
        else:
            q = _float_non_neg(raw_q, errors, qty_key, max_val=1e12)
        u = (data.get(unit_key) or "").strip().lower()
        if u and u not in prod_units_allowed:
            errors[unit_key] = "Select a unit (kg, sacks, tons)."
        if q not in (None,) and float(q or 0) > 0 and not u:
            errors[unit_key] = "Select unit for produced quantity."
        elif u and (q is None or float(q or 0) <= 0):
            errors[qty_key] = "Enter quantity when a unit is selected."
        qty_out = float(q) if isinstance(q, (int, float)) else 0.0
        production[v] = {"qty": qty_out, "unit": u}

    if not _truthy(data.get("agree_registration")):
        errors["agree_registration"] = "Confirm that your registration details are true and complete."

    if errors:
        return None, errors

    farm_size = _plant_area_to_hectares(plant_area_value, plant_area_unit)

    tree_bits = []
    for v in varieties:
        t = trees.get(v) or {}
        tree_bits.append(
            f"{v.title()}: {t.get('bearing', 0)} bearing / {t.get('non_bearing', 0)} non-bearing"
        )
    prod_bits = []
    for v in varieties:
        p = production.get(v) or {}
        qv = p.get("qty") or 0
        if qv > 0 and p.get("unit"):
            prod_bits.append(f"{v.title()} {float(qv):g} {p.get('unit')}")

    prod_year = str((data.get("production_year") or "2026")).strip()[:8] or "2026"

    farm_address = (
        f"Farm profile — Ownership: {ownership_status}; Plant area: {plant_area_value} {plant_area_unit} "
        f"({farm_size:.4f} ha est.). Trees: {'; '.join(tree_bits)}. "
        f"Production ({prod_year}): {', '.join(prod_bits) if prod_bits else '(not declared)'}. "
        f"Affiliation — Federation/group: {federation}; Growers assoc.: {association}. "
        f"RSBSA: {rsbsa_registered}; RSBSA No.: {rsbsa_number if rsbsa_number else '(n/a)'}. "
        f"Location: Barangay {barangay}, {municipality}, {province}."
    )
    if len(farm_address) > 3900:
        farm_address = farm_address[:3897] + "..."

    trees_json = json.dumps({k: trees[k] for k in varieties if trees.get(k) is not None})
    prod_json_clean = {}
    for k, pv in production.items():
        prod_json_clean[k] = {"qty": float(pv["qty"]), "unit": pv.get("unit") or ""}
    production_json = json.dumps(prod_json_clean)

    return {
        "name": name,
        "email": email,
        "phone": phone,
        "region": region,
        "province": province,
        "municipality": municipality,
        "barangay": barangay,
        "farm_address": farm_address,
        "farm_size": float(farm_size),
        "first_name": first_name,
        "last_name": last_name,
        "federation": federation,
        "association": association,
        "rsbsa_registered": rsbsa_registered,
        "rsbsa_number": rsbsa_number,
        "ownership_status": ownership_status,
        "plant_area_value": float(plant_area_value),
        "plant_area_unit": plant_area_unit,
        "trees_json": trees_json,
        "production_json": production_json,
        "profile_photo_data": _sanitize_profile_photo_data(data),
    }, {}


def validate_application_payload(data, cursor):
    errors = {}
    if not isinstance(data, dict):
        return None, {"_error": "Request body must be JSON."}

    try:
        farmer_id = int(data.get("farmer_id"))
    except (TypeError, ValueError):
        errors["farmer_id"] = "Farmer ID must be a whole number."
        farmer_id = None
    if farmer_id is not None and farmer_id < 1:
        errors["farmer_id"] = "Farmer ID must be a positive number."

    try:
        variety_id = int(data.get("coffee_variety_id"))
    except (TypeError, ValueError):
        errors["coffee_variety_id"] = "Select a coffee variety."
        variety_id = None
    if variety_id is not None and variety_id < 1:
        errors["coffee_variety_id"] = "Invalid coffee variety."

    farm_location = (data.get("farm_location") or "").strip()
    if len(farm_location) < 40:
        errors["farm_location"] = "Describe the farm location in at least 40 characters (access routes, terrain, nearest landmarks)."
    elif len(farm_location) > 2000:
        errors["farm_location"] = "Farm location must be at most 2000 characters."

    elevation = None
    el_raw = data.get("elevation")
    if el_raw not in (None, ""):
        try:
            elevation = float(el_raw)
        except (TypeError, ValueError):
            errors["elevation"] = "Elevation must be a valid number."
        else:
            if elevation < 0 or elevation > 9000:
                errors["elevation"] = "Elevation must be between 0 and 9,000 meters."

    def text_field(key, max_len, optional=True):
        v = (data.get(key) or "").strip()
        if not optional and len(v) < 1:
            return None, f"This field is required."
        if len(v) > max_len:
            return None, f"Must be at most {max_len} characters."
        return v, None

    soil_type, err = text_field("soil_type", 200)
    if err:
        errors["soil_type"] = err
    elif len((data.get("soil_type") or "").strip()) < 3:
        errors["soil_type"] = "Describe soil type in at least 3 characters (e.g. volcanic loam)."

    climate_info, err = text_field("climate_info", 2000)
    if err:
        errors["climate_info"] = err
    elif len((data.get("climate_info") or "").strip()) < 20:
        errors["climate_info"] = "Describe climate in at least 20 characters (rainfall pattern, mist, dry months)."

    cultivation_methods, err = text_field("cultivation_methods", 2000)
    if err:
        errors["cultivation_methods"] = err
    elif len((data.get("cultivation_methods") or "").strip()) < 20:
        errors["cultivation_methods"] = "Describe cultivation in at least 20 characters (shade, pruning, organic practices)."

    processing_methods, err = text_field("processing_methods", 2000)
    if err:
        errors["processing_methods"] = err
    elif len((data.get("processing_methods") or "").strip()) < 20:
        errors["processing_methods"] = "Describe processing in at least 20 characters (washed, natural, drying)."

    unique_characteristics, err = text_field("unique_characteristics", 2000)
    if err:
        errors["unique_characteristics"] = err
    elif len((data.get("unique_characteristics") or "").strip()) < 30:
        errors["unique_characteristics"] = "Explain geographical uniqueness in at least 30 characters (terroir, cup profile)."

    historical_significance, err = text_field("historical_significance", 2000)
    if err:
        errors["historical_significance"] = err

    if not _truthy(data.get("agree_declaration")):
        errors["agree_declaration"] = "You must accept the declaration: information is accurate to the best of your knowledge."

    if farmer_id is not None:
        cursor.execute("SELECT id FROM farmers WHERE id = ?", (farmer_id,))
        if not cursor.fetchone():
            errors["farmer_id"] = "Farmer ID not found. Register your farm first."

    if variety_id is not None:
        cursor.execute("SELECT id FROM coffee_varieties WHERE id = ?", (variety_id,))
        if not cursor.fetchone():
            errors["coffee_variety_id"] = "Invalid coffee variety."

    if errors:
        return None, errors

    return {
        "farmer_id": farmer_id,
        "coffee_variety_id": variety_id,
        "farm_location": farm_location,
        "elevation": elevation,
        "soil_type": soil_type or "",
        "climate_info": climate_info or "",
        "cultivation_methods": cultivation_methods or "",
        "processing_methods": processing_methods or "",
        "unique_characteristics": unique_characteristics or "",
        "historical_significance": historical_significance or "",
    }, {}


class RegisterFarmModule:
    def __init__(self, app):
        self.app = app
        self.init_database()
        self.setup_routes()
    
    def init_database(self):
        """Initialize SQLite database for Register Farm data"""
        if not os.path.exists(REGISTER_DB_PATH):
            # One-time compatibility migration from old DB locations.
            for source_path in [ROOT_REGISTER_DB_COMPAT_PATH] + LEGACY_DB_PATHS:
                if os.path.exists(source_path):
                    try:
                        shutil.copyfile(source_path, REGISTER_DB_PATH)
                        break
                    except Exception:
                        continue

        if not os.path.exists(REGISTER_DB_PATH):
            conn = sqlite3.connect(REGISTER_DB_PATH)
            cursor = conn.cursor()
            
            # Create farmers table
            cursor.execute('''
                CREATE TABLE farmers (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    phone TEXT,
                    region TEXT NOT NULL,
                    province TEXT NOT NULL,
                    municipality TEXT NOT NULL,
                    barangay TEXT NOT NULL DEFAULT '',
                    farm_address TEXT NOT NULL,
                    farm_size REAL,
                    first_name TEXT NOT NULL DEFAULT '',
                    last_name TEXT NOT NULL DEFAULT '',
                    federation TEXT NOT NULL DEFAULT '',
                    association TEXT NOT NULL DEFAULT '',
                    rsbsa_registered TEXT NOT NULL DEFAULT '',
                    rsbsa_number TEXT NOT NULL DEFAULT '',
                    ownership_status TEXT NOT NULL DEFAULT '',
                    plant_area_value REAL,
                    plant_area_unit TEXT NOT NULL DEFAULT '',
                    trees_json TEXT NOT NULL DEFAULT '{}',
                    production_json TEXT NOT NULL DEFAULT '{}',
                    profile_photo_data TEXT NOT NULL DEFAULT '',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ''')
            
            # Create coffee_varieties table
            cursor.execute('''
                CREATE TABLE coffee_varieties (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    description TEXT,
                    characteristics TEXT
                )
            ''')
            
            # Create gi_applications table
            cursor.execute('''
                CREATE TABLE gi_applications (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    farmer_id INTEGER NOT NULL,
                    coffee_variety_id INTEGER NOT NULL,
                    farm_location TEXT NOT NULL,
                    elevation REAL,
                    soil_type TEXT,
                    climate_info TEXT,
                    cultivation_methods TEXT,
                    processing_methods TEXT,
                    unique_characteristics TEXT,
                    historical_significance TEXT,
                    status TEXT DEFAULT 'pending',
                    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    review_date TIMESTAMP,
                    reviewer_notes TEXT,
                    FOREIGN KEY (farmer_id) REFERENCES farmers (id),
                    FOREIGN KEY (coffee_variety_id) REFERENCES coffee_varieties (id)
                )
            ''')
            
            # Create gi_certifications table
            cursor.execute('''
                CREATE TABLE gi_certifications (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    application_id INTEGER NOT NULL,
                    certification_number TEXT UNIQUE NOT NULL,
                    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    expiry_date TIMESTAMP,
                    certification_status TEXT DEFAULT 'active',
                    FOREIGN KEY (application_id) REFERENCES gi_applications (id)
                )
            ''')
            
            # Insert sample coffee varieties
            cursor.execute('''
                INSERT INTO coffee_varieties (name, description, characteristics) VALUES
                ('Liberica', 'Large coffee beans with unique floral aroma', 'Bold, woody, floral notes, large bean size'),
                ('Robusta', 'High caffeine content coffee variety', 'Strong, bitter, high caffeine, disease resistant'),
                ('Excelsa', 'Tart and fruity coffee variety', 'Tart, fruity, light body, complex flavor'),
                ('Arabica', 'Premium coffee variety with mild flavor', 'Mild, sweet, complex, low caffeine')
            ''')
            
            conn.commit()
            conn.close()
        else:
            # Ensure schema upgrades for existing DB.
            conn = sqlite3.connect(REGISTER_DB_PATH)
            cursor = conn.cursor()
            cursor.execute("PRAGMA table_info(farmers)")
            cols = {row[1] for row in cursor.fetchall()}
            if "barangay" not in cols:
                cursor.execute("ALTER TABLE farmers ADD COLUMN barangay TEXT NOT NULL DEFAULT ''")
            extra_cols = [
                ("first_name", "TEXT NOT NULL DEFAULT ''"),
                ("last_name", "TEXT NOT NULL DEFAULT ''"),
                ("federation", "TEXT NOT NULL DEFAULT ''"),
                ("association", "TEXT NOT NULL DEFAULT ''"),
                ("rsbsa_registered", "TEXT NOT NULL DEFAULT ''"),
                ("rsbsa_number", "TEXT NOT NULL DEFAULT ''"),
                ("ownership_status", "TEXT NOT NULL DEFAULT ''"),
                ("plant_area_value", "REAL"),
                ("plant_area_unit", "TEXT NOT NULL DEFAULT ''"),
                ("trees_json", "TEXT NOT NULL DEFAULT '{}'"),
                ("production_json", "TEXT NOT NULL DEFAULT '{}'"),
                ("profile_photo_data", "TEXT NOT NULL DEFAULT ''"),
            ]
            for cn, decl in extra_cols:
                if cn not in cols:
                    cursor.execute(f"ALTER TABLE farmers ADD COLUMN {cn} {decl}")
                conn.commit()
            conn.close()
    
    def setup_routes(self):
        """Setup API routes for Register Farm module"""
        
        @self.app.route('/api/register-farm/applications', methods=['POST'])
        def submit_gi_application():
            data = request.get_json(silent=True)
            conn = sqlite3.connect(REGISTER_DB_PATH)
            cursor = conn.cursor()
            cleaned, errs = validate_application_payload(data, cursor)
            if errs:
                conn.close()
                return jsonify({'success': False, 'errors': errs}), 400
            try:
                cursor.execute('''
                    INSERT INTO gi_applications 
                    (farmer_id, coffee_variety_id, farm_location, elevation, soil_type, 
                     climate_info, cultivation_methods, processing_methods, unique_characteristics, 
                     historical_significance)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ''', (
                    cleaned['farmer_id'], cleaned['coffee_variety_id'], cleaned['farm_location'],
                    cleaned['elevation'], cleaned['soil_type'], cleaned['climate_info'],
                    cleaned['cultivation_methods'], cleaned['processing_methods'],
                    cleaned['unique_characteristics'], cleaned['historical_significance'],
                ))
                application_id = cursor.lastrowid
                conn.commit()
                conn.close()
                return jsonify({
                    'success': True,
                    'application_id': application_id,
                    'message': 'Register farm application submitted successfully',
                })
            except Exception as e:
                conn.close()
                return jsonify({'success': False, 'errors': {'_error': str(e)}}), 400
        
        @self.app.route('/api/register-farm/varieties', methods=['GET'])
        def get_coffee_varieties():
            try:
                conn = sqlite3.connect(REGISTER_DB_PATH)
                cursor = conn.cursor()
                
                cursor.execute('SELECT * FROM coffee_varieties')
                varieties = cursor.fetchall()
                
                conn.close()
                
                return jsonify({
                    'success': True,
                    'varieties': [
                        {
                            'id': v[0], 'name': v[1], 'description': v[2], 
                            'characteristics': v[3]
                        } for v in varieties
                    ]
                })
            except Exception as e:
                return jsonify({'success': False, 'error': str(e)}), 500
        
        @self.app.route('/api/register-farm/applications/<int:farmer_id>', methods=['GET'])
        def get_farmer_applications(farmer_id):
            try:
                conn = sqlite3.connect(REGISTER_DB_PATH)
                cursor = conn.cursor()
                
                cursor.execute('''
                    SELECT ga.*, cv.name as variety_name, f.name as farmer_name
                    FROM gi_applications ga
                    JOIN coffee_varieties cv ON ga.coffee_variety_id = cv.id
                    JOIN farmers f ON ga.farmer_id = f.id
                    WHERE ga.farmer_id = ?
                    ORDER BY ga.submission_date DESC
                ''', (farmer_id,))
                
                applications = cursor.fetchall()
                conn.close()
                
                return jsonify({
                    'success': True,
                    'applications': [
                        {
                            'id': app[0], 'farmer_id': app[1], 'coffee_variety_id': app[2],
                            'farm_location': app[3], 'elevation': app[4], 'soil_type': app[5],
                            'climate_info': app[6], 'cultivation_methods': app[7],
                            'processing_methods': app[8], 'unique_characteristics': app[9],
                            'historical_significance': app[10], 'status': app[11],
                            'submission_date': app[12], 'review_date': app[13],
                            'reviewer_notes': app[14], 'variety_name': app[15], 'farmer_name': app[16]
                        } for app in applications
                    ]
                })
            except Exception as e:
                return jsonify({'success': False, 'error': str(e)}), 500

        @self.app.route('/api/register-farm/farmer-profile', methods=['GET'])
        def get_farmer_profile_by_login():
            """Fetch saved farmer registration profile by account login id (email/phone)."""
            try:
                raw_login = (request.args.get('login_id') or '').strip().lower()
                if not raw_login:
                    return jsonify({'success': False, 'error': 'Missing login_id'}), 400

                def phone_variants(v: str):
                    s = re.sub(r'\D+', '', v or '')
                    out = set()
                    if not s:
                        return out
                    if s.startswith('63') and len(s) >= 12:
                        out.add('0' + s[2:])
                    if s.startswith('0') and len(s) >= 11:
                        out.add('+63' + s[1:])
                    if len(s) == 10 and s.startswith('9'):
                        out.add('0' + s)
                        out.add('+63' + s)
                    out.add(v.strip())
                    out.add(s)
                    return {x.lower() for x in out if x}

                variants = phone_variants(raw_login)
                conn = sqlite3.connect(REGISTER_DB_PATH)
                conn.row_factory = sqlite3.Row
                cursor = conn.cursor()

                row = None
                if '@' in raw_login:
                    cursor.execute(
                        "SELECT * FROM farmers WHERE lower(email) = ? ORDER BY id DESC LIMIT 1",
                        (raw_login,),
                    )
                    row = cursor.fetchone()
                else:
                    # Try phone matches first.
                    for ph in variants:
                        cursor.execute(
                            "SELECT * FROM farmers WHERE lower(phone) = ? ORDER BY id DESC LIMIT 1",
                            (ph,),
                        )
                        row = cursor.fetchone()
                        if row:
                            break
                    # Fallback: sometimes login id may still be in email column.
                    if not row:
                        cursor.execute(
                            "SELECT * FROM farmers WHERE lower(email) = ? ORDER BY id DESC LIMIT 1",
                            (raw_login,),
                        )
                        row = cursor.fetchone()

                conn.close()
                if not row:
                    return jsonify({'success': True, 'found': False, 'profile': None})

                def _safe_json(raw, default):
                    try:
                        return json.loads(raw) if raw else default
                    except Exception:
                        return default

                trees = _safe_json(row['trees_json'], {})
                production = _safe_json(row['production_json'], {})
                try:
                    photo_raw = (row['profile_photo_data'] or '').strip()
                except (KeyError, IndexError):
                    photo_raw = ''
                profile = {
                    'id': row['id'],
                    'name': row['name'] or '',
                    'first_name': row['first_name'] or '',
                    'last_name': row['last_name'] or '',
                    'email': row['email'] or '',
                    'phone': row['phone'] or '',
                    'province': row['province'] or '',
                    'municipality': row['municipality'] or '',
                    'barangay': row['barangay'] or '',
                    'federation': row['federation'] or '',
                    'association': row['association'] or '',
                    'ncfrs': 'yes' if (str(row['association'] or '').strip().lower() == 'yes') else (
                        'no' if str(row['association'] or '').strip().lower() == 'no' else ''
                    ),
                    'rsbsa_registered': row['rsbsa_registered'] or '',
                    'rsbsa_number': row['rsbsa_number'] or '',
                    'ownership_status': row['ownership_status'] or '',
                    'plant_area_value': row['plant_area_value'],
                    'plant_area_unit': row['plant_area_unit'] or '',
                    'tree_counts': trees if isinstance(trees, dict) else {},
                    'production': production if isinstance(production, dict) else {},
                    'production_year': '2026',
                    'profile_photo_data': photo_raw,
                }
                return jsonify({'success': True, 'found': True, 'profile': profile})
            except Exception as e:
                return jsonify({'success': False, 'error': str(e)}), 500
        
        @self.app.route('/register-farm')
        def register_farm_portal():
            """Serve Register Farm portal page"""
            return render_template_string(self.get_register_farm_portal_html())
    
    def get_register_farm_portal_html(self):
        """Return HTML for Register Farm portal"""
        return '''
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#25671E">
    <meta name="color-scheme" content="light">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>Register Farm Portal - Beanthentic Coffee</title>
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/responsive.css">
    <script id="beanthentic-fixed-topbar-js" src="/js/beanthentic_fixed_topbar.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
            color: #1a1a1a;
            background: #e9eef4;
            line-height: 1.6;
            min-height: 100vh;
            min-height: 100dvh;
            padding: max(0px, env(safe-area-inset-top)) max(0px, env(safe-area-inset-right)) max(0px, env(safe-area-inset-bottom)) max(0px, env(safe-area-inset-left));
            -webkit-tap-highlight-color: rgba(37, 103, 30, 0.12);
        }
        /* Farmer registration: green header stays put; only the white form scrolls */
        html {
            height: 100%;
        }
        body.register-farm-flow {
            min-height: 100%;
            display: block;
            overflow-y: auto;
            overflow-x: hidden;
            padding-top: 0 !important;
        }
        body.register-farm-flow.beanthentic-fixed-topbar-active {
            padding-top: var(--beanthentic-fixed-topbar-h, 0px) !important;
        }
        body.register-farm-flow.has-app-bottom-nav {
            padding-bottom: 0 !important;
        }
        body.register-farm-flow .app-bottom-nav {
            display: none !important;
        }
        body.register-farm-flow .main-content.register-farm-main {
            display: block;
            overflow: visible;
            padding: 0;
        }
        body.register-farm-flow .main-content.register-farm-main > .container {
            display: block;
            overflow: visible;
            max-width: none;
            width: 100%;
            margin: 0;
            padding-left: 0;
            padding-right: 0;
        }
        .fr-reg-shell {
            display: block;
            overflow: visible;
            width: 100%;
            max-width: none;
            margin-left: 0;
            margin-right: 0;
        }
        .fr-reg-hero {
            background: linear-gradient(165deg, #1b5e20 0%, #145218 55%, #0f3d12 100%);
            border-radius: 0 0 20px 20px;
            padding-top: max(0.45rem, env(safe-area-inset-top));
            padding-bottom: 1.75rem;
            padding-left: max(1.05rem, env(safe-area-inset-left));
            padding-right: max(1.05rem, env(safe-area-inset-right));
            color: #fff;
            box-shadow: 0 8px 24px rgba(20, 82, 24, 0.22);
            flex-shrink: 0;
            z-index: 8;
            width: 100%;
        }
        .fr-reg-hero-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            text-align: center;
            position: relative;
        }
        .fr-reg-hero-inner > div:last-child {
            min-width: 0;
        }
        .fr-reg-nav-back {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #ffffff;
            padding: 0;
            line-height: 0;
            cursor: pointer;
        }
        .fr-reg-nav-back svg {
            width: 26px;
            height: 26px;
        }
        @media (max-width: 520px) {
            .fr-reg-nav-back svg { width: 24px; height: 24px; }
        }
        .fr-reg-title {
            font-size: 1.22rem;
            font-weight: 700;
            line-height: 1.12;
            margin: 0 0 0.12rem;
            letter-spacing: -0.02em;
        }
        .fr-reg-tagline {
            margin: 0;
            font-size: 0.74rem;
            line-height: 1.3;
            opacity: 0.95;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.92);
        }
        .register-farm-sheet.fr-reg-sheet {
            background: #fff;
            border-radius: 16px 16px 0 0;
            padding-top: 2.35rem;
            padding-bottom: calc(1.85rem + 4.85rem + env(safe-area-inset-bottom, 0px));
            padding-left: max(1.05rem, env(safe-area-inset-left));
            padding-right: max(1.05rem, env(safe-area-inset-right));
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-bottom: none;
            margin-top: -0.75rem;
            position: relative;
            z-index: 2;
            min-height: 60vh;
            overflow-x: hidden;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            width: 100%;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding-left: max(16px, env(safe-area-inset-left));
            padding-right: max(16px, env(safe-area-inset-right));
        }
        .app-shell-intro {
            margin-bottom: 1.75rem;
        }
        .app-tagline {
            font-size: 0.9375rem;
            color: #4b5563;
            margin-bottom: 1.25rem;
            line-height: 1.55;
            max-width: 52rem;
        }
        .process-rail {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .process-step {
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(37, 103, 30, 0.15);
            border-radius: 14px;
            padding: 1rem 0.85rem;
            text-align: center;
            box-shadow: 0 4px 14px rgba(17, 24, 39, 0.06);
            width: min(100%, 470px);
        }
        .process-step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: linear-gradient(135deg, #25671E 0%, #25671E 100%);
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .process-step-num svg {
            width: 14px;
            height: 14px;
        }
        .process-step h3 {
            font-size: 0.88rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .process-step p {
            font-size: 0.78rem;
            color: #6b7280;
            line-height: 1.4;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .detail-tile {
            background: white;
            border-radius: 14px;
            padding: 1.15rem 1.2rem;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 6px 20px rgba(17, 24, 39, 0.06);
        }
        .detail-tile h4 {
            font-size: 0.82rem;
            font-weight: 600;
            color: #25671E;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .detail-tile h4 svg { width: 1rem; height: 1rem; }
        .detail-tile ul {
            margin: 0;
            padding-left: 1.1rem;
            font-size: 0.875rem;
            color: #4b5563;
            line-height: 1.5;
        }
        .detail-tile li { margin-bottom: 0.35rem; }
        .callout-info {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid rgba(37, 103, 30, 0.2);
            border-radius: 12px;
            padding: 1rem 1.15rem;
            font-size: 0.875rem;
            color: #065f46;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .callout-info strong { color: #047857; }
        
        .header {
            background: linear-gradient(135deg, #25671E 0%, #25671E 100%);
            color: white;
            padding: 3rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }
        
        .header h1 {
            font-size: clamp(1.35rem, 3.5vw, 1.65rem);
            font-weight: 700;
            margin-bottom: 0.35rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header p {
            font-size: 0.95rem;
            opacity: 0.92;
            max-width: 36rem;
            margin: 0 auto;
            line-height: 1.45;
        }
        
        .header-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 0.5rem;
            color: #b8e8c4;
        }
        .header-icon svg, .card-icon svg, .back-link svg, .btn svg, .tab svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
        .header-icon svg { width: 2rem; height: 2rem; }
        .card-icon svg { width: 1.5rem; height: 1.5rem; }
        .field-error {
            display: block;
            font-size: 0.8125rem;
            color: #b91c1c;
            margin-top: 0.55rem;
            min-height: 1.15em;
        }
        input.input-invalid, select.input-invalid, textarea.input-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
        }
        .status-badge svg { width: 1rem; height: 1rem; vertical-align: middle; margin-right: 0.25rem; }
        
        .main-content {
            padding: 1.25rem 0 1.75rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #25671E;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: #25671E;
            transform: translateX(-3px);
        }
        
        .tabs {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            padding: 0.5rem;
            margin-bottom: 2rem;
            gap: 0.5rem;
        }
        
        .tab {
            flex: 1 0 auto;
            min-width: 140px;
            min-height: 48px;
            padding: 0.75rem 1rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #6b7280;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .tab:hover {
            background: #f9fafb;
            color: #374151;
        }
        
        .tab.active {
            background: linear-gradient(135deg, #25671E 0%, #25671E 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(37, 103, 30, 0.3);
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 2.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #25671E 0%, #25671E 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .card-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            margin-top: 0.25rem;
        }
        
        .status-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1rem;
            align-items: stretch;
        }
        .status-row input {
            flex: 1 1 200px;
            min-width: 0;
        }
        .status-row .btn {
            flex: 0 0 auto;
        }
        @media (max-width: 480px) {
            .status-row { flex-direction: column; }
            .status-row .btn { width: 100%; justify-content: center; }
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.95rem;
        }
        
        .required {
            color: #ef4444;
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: #25671E;
            box-shadow: 0 0 0 3px rgba(37, 103, 30, 0.1);
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-help {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        
        .btn {
            background: linear-gradient(135deg, #25671E 0%, #25671E 100%);
            color: white;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px rgba(37, 103, 30, 0.2);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(37, 103, 30, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        .btn.is-loading {
            cursor: wait;
            pointer-events: none;
            transform: none;
            opacity: 0.95;
        }
        .btn-spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 999px;
            animation: gi-btn-spin 0.75s linear infinite;
        }
        @keyframes gi-btn-spin {
            to { transform: rotate(360deg); }
        }
        
        .btn-secondary {
            background: #6b7280;
            box-shadow: 0 4px 6px rgba(107, 114, 128, 0.2);
        }
        
        .btn-secondary:hover {
            background: #4b5563;
            box-shadow: 0 8px 12px rgba(107, 114, 128, 0.3);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .application-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .application-item:hover {
            border-color: #25671E;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .application-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .application-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-weight: 500;
            color: #1f2937;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-icon {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
        .empty-apps {
            text-align: center;
            padding: 2rem 1rem;
            color: #6b7280;
        }
        .empty-apps svg {
            width: 3rem;
            height: 3rem;
            margin-bottom: 1rem;
            opacity: 0.45;
            color: #25671E;
        }
        .apps-heading {
            margin-bottom: 1.5rem;
            color: #1f2937;
            font-size: 1.125rem;
            font-weight: 600;
        }
        .variety-line {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .variety-line svg { width: 1rem; height: 1rem; }
        .reviewer-box {
            margin-top: 1rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            border-left: 4px solid #25671E;
        }
        .reviewer-title {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .reviewer-title svg { width: 1rem; height: 1rem; }
        .reviewer-text { color: #6b7280; font-size: 0.875rem; }
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            font-size: 0.9rem;
            color: #374151;
            line-height: 1.45;
            cursor: pointer;
            max-width: 40rem;
        }
        .checkbox-label input[type="checkbox"] {
            width: 1.15rem;
            height: 1.15rem;
            margin-top: 0.2rem;
            flex-shrink: 0;
            accent-color: #25671E;
        }
        .checkbox-label.checkbox-invalid {
            outline: 2px solid #ef4444;
            outline-offset: 2px;
            border-radius: 10px;
            padding: 0.35rem 0.25rem;
        }
        
        .progress-bar {
            background: #e5e7eb;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .progress-fill {
            background: linear-gradient(90deg, #25671E 0%, #25671E 100%);
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #25671E;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .fr-section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #3d2914;
            margin: 2.1rem 0 1.05rem;
            letter-spacing: -0.01em;
        }
        /* Profile preview on step 1 — mirrors photo chosen on Profile Picture step */
        .fr-hero-avatar-wrap {
            display: flex;
            justify-content: center;
            padding: 0.15rem 0 0.35rem;
        }
        .fr-hero-avatar-btn {
            border: 0;
            padding: 0;
            margin: 0;
            background: transparent;
            cursor: pointer;
            border-radius: 999px;
            -webkit-tap-highlight-color: transparent;
        }
        .fr-hero-avatar-btn:focus-visible {
            outline: 2px solid #1b5e20;
            outline-offset: 3px;
        }
        .fr-hero-avatar-btn:active .fr-hero-avatar {
            transform: scale(0.97);
        }
        .fr-hero-avatar {
            width: min(28vw, 112px);
            height: min(28vw, 112px);
            border-radius: 999px;
            background: #e5e7eb;
            border: 1px solid #d1d5db;
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 1px 4px rgba(15, 23, 42, 0.08);
            transition: transform 0.15s ease;
        }
        .fr-hero-avatar-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 999px;
            z-index: 1;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .fr-hero-avatar.has-photo .fr-hero-avatar-img {
            opacity: 1;
            visibility: visible;
        }
        .fr-hero-avatar-placeholder {
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s ease;
        }
        .fr-hero-avatar-placeholder svg {
            width: 52px;
            height: 52px;
        }
        .fr-hero-avatar.has-photo .fr-hero-avatar-placeholder {
            opacity: 0;
        }
        /* First section (e.g. Personal Information): extra space below green header */
        .fr-section-title:first-of-type {
            margin-top: 0.35rem;
        }
        .fr-block-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: #111111;
            margin: 0 0 0.55rem;
        }
        .fr-affiliation-fed .fr-field { margin-bottom: 0; }
        .fr-block-label:first-child { margin-top: 0; }
        .fr-label-placeholder {
            visibility: hidden;
            display: block;
            margin-bottom: 0.32rem;
            min-height: 0.78rem;
            font-size: 0.78rem;
        }
        .fr-fed-block {
            margin-top: 0.65rem;
        }
        .fr-fed-block .fr-block-label {
            margin-bottom: 0.55rem;
        }
        .fr-fed-row {
            display: grid;
            grid-template-columns: minmax(0, 0.88fr) minmax(0, 1.42fr);
            gap: 1.3rem 1rem;
            align-items: start;
        }
        @media (max-width: 480px) {
            .fr-fed-row { grid-template-columns: 1fr; }
        }
        .fr-row2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem 1rem;
        }
        .fr-row3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem 0.85rem;
        }
        .fr-field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #111111;
            margin-bottom: 0.45rem;
        }
        .fr-field input,
        .fr-field select {
            width: 100%;
            padding: 0.68rem 0.8rem;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
            font-size: 0.94rem;
            font-family: inherit;
        }
        .fr-field input:focus,
        .fr-field select:focus {
            outline: none;
            border-color: #2273eb;
            box-shadow: 0 0 0 3px rgba(34, 115, 235, 0.22);
        }
        .fr-page-heading {
            font-size: 1.35rem;
            font-weight: 700;
            color: #3d2914;
            margin: 0 0 1.35rem;
            letter-spacing: -0.02em;
        }
        .fr-subsection-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #374151;
            margin: -0.2rem 0 0.5rem;
        }
        .fr-field input[readonly] {
            background: #f3f4f6;
            color: #6b7280;
        }
        .fr-step { display: none; }
        .fr-step.is-active { display: block; animation: fadeIn 0.35s ease; }
        .fr-wizard-foot {
            margin-top: 2rem;
            padding-top: 1.35rem;
            border-top: 1px solid #eef0f4;
            display: flex;
            gap: 0.65rem;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .fr-wizard-foot.fr-wizard-foot--first { justify-content: flex-end; }
        .btn-fr-back {
            background: #f1f4f8;
            color: #1c5216;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 0.7rem 1.2rem;
            font-weight: 700;
            font-size: 0.92rem;
            font-family: inherit;
            cursor: pointer;
            display: none;
            align-items: center;
            gap: 0.35rem;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06);
        }
        .btn-fr-back.is-visible { display: inline-flex; }
        .btn-fr-next {
            background: linear-gradient(135deg, #1b5e20, #145218);
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 0.7rem 1.35rem;
            font-weight: 700;
            font-size: 0.92rem;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            box-shadow: 0 4px 14px rgba(28, 82, 22, 0.28);
        }
        .btn-fr-submit {
            display: none;
        }
        .btn-fr-submit.is-visible {
            display: inline-flex;
        }
        .btn-fr-submit.is-loading, .btn-fr-next.is-loading {
            cursor: wait;
            opacity: 0.92;
            pointer-events: none;
        }
        .fr-split-area {
            display: grid;
            grid-template-columns: 1fr minmax(6.8rem, 7.5rem);
            gap: 0.45rem;
        }
        .fr-tree-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.55rem 1.05rem;
        }
        .fr-tree-grid .fr-field label.fr-tree-label {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.22rem;
            margin-bottom: 0.5rem;
            font-size: inherit;
            font-weight: inherit;
            color: inherit;
        }
        .fr-tree-grid .fr-tree-variety {
            font-size: 0.96rem;
            font-weight: 700;
            color: #3d2914;
            line-height: 1.28;
            letter-spacing: -0.01em;
        }
        .fr-tree-grid .fr-tree-metric {
            font-size: 0.78rem;
            font-weight: 600;
            color: #4b5563;
            line-height: 1.35;
        }
        .fr-prod-block { margin-bottom: 0.50rem; }
        .fr-prod-block .fr-prod-variety {
            font-size: 0.84rem;
            font-weight: 700;
            color: #3d2914;
            margin-bottom: 0.15rem;
        }
        .fr-prod-pair {
            display: grid;
            grid-template-columns: 1fr minmax(5.8rem, 6.75rem);
            gap: 0.55rem;
        }
        .fr-prod-pair input,
        .fr-prod-pair select {
            width: 100%;
            padding: 0.68rem 0.8rem;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
            font-size: 0.94rem;
            font-family: inherit;
        }
        .fr-prod-pair input:focus,
        .fr-prod-pair select:focus {
            outline: none;
            border-color: #2273eb;
            box-shadow: 0 0 0 3px rgba(34, 115, 235, 0.22);
        }
        .fr-photo-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.95rem;
            position: relative;
        }
        .fr-photo-toast {
            position: absolute;
            left: 50%;
            top: -3.2rem;
            transform: translateX(-50%);
            width: min(520px, calc(100% - 24px));
            background: rgba(254, 242, 242, 0.98);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #7f1d1d;
            border-radius: 12px;
            padding: 0.9rem 0.95rem;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.18);
            display: none;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            z-index: 6;
        }
        .fr-photo-toast.is-open { display: flex; }
        .fr-photo-toast.success {
            background: rgba(236, 253, 245, 0.98);
            border-color: rgba(34, 197, 94, 0.35);
            color: #065f46;
        }
        .fr-photo-toast .toast-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        .fr-photo-toast .toast-text {
            font-size: 0.92rem;
            line-height: 1.35;
            font-weight: 600;
        }
        .fr-photo-avatar-wrap {
            position: relative;
            width: min(72vw, 230px);
            height: min(72vw, 230px);
            border-radius: 999px;
            overflow: hidden;
            background: #e5e7eb;
            border: 1px solid #d1d5db;
            box-shadow: inset 0 1px 4px rgba(15, 23, 42, 0.08);
        }
        .fr-photo-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .fr-photo-icon-btn {
            position: absolute;
            right: 0.5rem;
            bottom: 0.5rem;
            width: 2rem;
            height: 2rem;
            border: 1px solid #d1d5db;
            border-radius: 999px;
            background: #ffffff;
            color: #111827;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.18);
            cursor: pointer;
        }
        .fr-photo-icon-btn svg { width: 1rem; height: 1rem; }
        .fr-photo-actions {
            width: min(100%, 300px);
            display: grid;
            gap: 0.75rem;
        }
        .btn-fr-photo {
            width: 100%;
            border-radius: 10px;
            padding: 0.72rem 1rem;
            font-weight: 700;
            font-size: 0.95rem;
            font-family: inherit;
            cursor: pointer;
        }
        .btn-fr-photo.take {
            border: 1px solid #d1d5db;
            background: #f8fafc;
            color: #1c5216;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        }
        .btn-fr-photo.upload {
            border: none;
            background: linear-gradient(135deg, #1b5e20, #145218);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(28, 82, 22, 0.28);
        }
        .fr-photo-hint {
            margin: 0;
            font-size: 0.82rem;
            color: #6b7280;
        }
        /* display:none breaks programmatic input.click() → file/camera sheet in Chrome/Edge/Safari */
        .fr-visually-hidden-file {
            position: absolute;
            left: 0;
            top: 0;
            width: 1px;
            height: 1px;
            margin: 0;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
            opacity: 0.01;
        }
        .fr-camera-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.72);
            z-index: 1200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .fr-camera-modal.is-open { display: flex; }
        .fr-camera-panel {
            width: min(92vw, 420px);
            background: #ffffff;
            border-radius: 16px;
            padding: 0.9rem;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.32);
        }
        .fr-camera-video {
            width: 100%;
            border-radius: 12px;
            background: #111827;
            aspect-ratio: 3 / 4;
            object-fit: cover;
        }
        .fr-camera-actions {
            margin-top: 0.8rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
        }
        .btn-fr-cam {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 0.65rem 0.85rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
        }
        .btn-fr-cam.capture {
            border: none;
            background: linear-gradient(135deg, #1b5e20, #145218);
            color: #fff;
        }
        .fr-terms-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2f2112;
            margin: 0 0 0.75rem;
            letter-spacing: -0.01em;
            line-height: 1.12;
        }
        .fr-terms-body {
            font-size: 0.98rem;
            line-height: 1.45;
            color: #c4c4c4;
            margin-bottom: 1rem;
        }
        .fr-submit-overlay {
            position: fixed;
            inset: 0;
            z-index: 1400;
            display: none;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1rem;
        }
        .fr-submit-overlay.is-visible { display: flex; }
        .fr-submit-overlay.loading {
            background: #ffffff;
            color: #111827;
        }
        .fr-loading-card {
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            font-weight: 600;
            color: #111827;
        }
        .fr-loading-bean {
            width: 96px;
            height: 96px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .fr-loading-bean img {
            width: 96px;
            height: 96px;
            object-fit: contain;
            animation: fr-loading-bean-bounce 1s ease-in-out infinite;
        }
        @keyframes fr-loading-bean-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .fr-loading-text {
            font-size: 0.88rem;
            font-weight: 500;
        }
        .fr-submit-overlay.success {
            background: linear-gradient(165deg, #0f3d12 0%, #145218 45%, #1f8a2f 100%);
            color: #fff;
            flex-direction: column;
            gap: 0.9rem;
            background-size: 160% 160%;
            animation: fr-success-bg 1.8s ease-out forwards;
        }
        .fr-success-actions {
            display: flex;
            justify-content: center;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-top: 0.25rem;
        }
        .fr-success-btn {
            border: 0;
            border-radius: 999px;
            padding: 0.72rem 1.25rem;
            font-weight: 900;
            cursor: pointer;
            font-family: inherit;
            background: rgba(255,255,255,0.14);
            color: #ffffff;
            box-shadow: 0 14px 34px rgba(0,0,0,0.22);
        }
        .fr-success-btn.primary {
            background: #ffffff;
            color: #145218;
        }
        .fr-submit-overlay.block {
            background: radial-gradient(circle at top, rgba(16,185,129,0.18), rgba(15, 23, 42, 0.92));
            color: #fff;
            flex-direction: column;
            gap: 0.9rem;
        }
        .fr-block-card {
            width: min(92vw, 520px);
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 18px;
            padding: 1.25rem 1.15rem;
            text-align: center;
            box-shadow: 0 18px 44px rgba(0,0,0,0.28);
        }
        .fr-block-title {
            margin: 0 0 0.35rem;
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .fr-block-body {
            margin: 0 0 1rem;
            font-size: 0.95rem;
            line-height: 1.45;
            opacity: 0.95;
        }
        .fr-block-actions {
            display: flex;
            justify-content: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }
        .fr-block-btn {
            border: 0;
            border-radius: 999px;
            padding: 0.7rem 1.25rem;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
        }
        .fr-block-btn.primary {
            background: #ffffff;
            color: #145218;
        }
        .fr-success-mark {
            width: 120px;
            height: 120px;
            border-radius: 999px;
            background: #fff;
            color: #1f8a2f;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.24);
            transform: scale(0.75);
            opacity: 0;
            animation: fr-success-pop 0.42s cubic-bezier(.2,.9,.25,1.25) 0.12s forwards;
        }
        .fr-success-mark svg {
            width: 64px;
            height: 64px;
            stroke-width: 2.6;
        }
        .fr-success-title {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
            margin: 0;
            opacity: 0;
            transform: translateY(8px);
            animation: fr-success-text 0.34s ease-out 0.42s forwards;
        }
        .fr-success-subtitle {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            opacity: 0.95;
            transform: translateY(8px);
            animation: fr-success-text 0.34s ease-out 0.52s forwards;
        }
        @keyframes fr-success-pop {
            0% { transform: scale(0.75); opacity: 0; }
            70% { transform: scale(1.06); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes fr-success-text {
            0% { opacity: 0; transform: translateY(8px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fr-success-bg {
            0% { background-position: 0% 0%; }
            100% { background-position: 50% 50%; }
        }
        @media (max-width: 620px) {
            .fr-row2 { grid-template-columns: 1fr; }
            .fr-row3 { grid-template-columns: 1fr; }
            .fr-tree-grid { grid-template-columns: 1fr; }
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 0.85rem 0 1rem;
            }
            .header h1 {
                font-size: clamp(1.22rem, 4.5vw, 1.45rem);
                padding: 0 0.5rem;
            }
            .header p { font-size: 0.88rem; padding: 0 0.5rem; }
            .main-content { padding: 1rem 0 1.5rem; }
            .container { padding: 0 16px; }
            .tabs {
                gap: 0.35rem;
                padding: 0.4rem;
            }
            .tab {
                min-width: 148px;
                font-size: 0.9rem;
                padding: 0.85rem 1rem;
            }
            .card {
                padding: 1.5rem;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .application-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            .process-rail {
                justify-content: center;
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="has-app-bottom-nav register-farm-flow">
    <div class="main-content register-farm-main">
        <div class="container">
            <div id="registerFarmSuccess" class="card" style="display:none; margin:0 0 14px; border:1px solid rgba(34,197,94,0.35); max-width:540px; margin-left:auto; margin-right:auto;">
                    <div class="card-header" style="padding:14px 16px;">
                        <div class="card-icon" style="background:rgba(34,197,94,0.12); color:#166534;">
                            <i data-lucide="check-circle"></i>
                        </div>
                        <div>
                            <h2 class="card-title" style="margin:0;">Successful</h2>
                            <p class="card-subtitle" style="margin:2px 0 0;">Data sent successfully.</p>
                        </div>
                    </div>
                </div>
            <div class="fr-reg-shell" id="frWizardCard">
                <header class="fr-reg-hero" aria-label="Registration header">
                    <div class="fr-reg-hero-inner">
                        <button type="button" id="frNavBack" class="fr-reg-nav-back" aria-label="Back">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18 9 12l6-6"/></svg>
                        </button>
                        <div>
                            <h1 class="fr-reg-title">Farmer&rsquo;s Registration</h1>
                            <p class="fr-reg-tagline"><em>Registered in Barako Federation Association</em></p>
                        </div>
                    </div>
                </header>
                <div class="register-farm-sheet fr-reg-sheet">
                    <form id="farmerForm" novalidate autocomplete="off">
                    <input type="hidden" name="production_year" id="productionYear" value="2026">
                    <input type="hidden" id="farmerEmail" name="email" value="">
                    <input type="hidden" id="farmerPhone" name="phone" value="">

                    <div id="frStep1" class="fr-step is-active" data-fr-step="1">
                        <div class="fr-hero-avatar-wrap">
                            <button type="button" class="fr-hero-avatar-btn" id="frHeroPickPhotoBtn" aria-label="Add profile photo — camera or gallery" title="Tap: camera or gallery">
                                <div class="fr-hero-avatar" id="frHeroAvatar">
                                    <img id="frHeroProfilePhoto" class="fr-hero-avatar-img" alt="" width="112" height="112" decoding="async" />
                                    <span class="fr-hero-avatar-placeholder"><i data-lucide="user"></i></span>
                                </div>
                            </button>
                        </div>
                        <p class="fr-section-title">Personal Information</p>
                        <div class="fr-row2">
                            <div class="fr-field">
                                <label for="farmerLastName">Last Name <span class="required">*</span></label>
                                <input type="text" id="farmerLastName" name="last_name" maxlength="80" autocomplete="family-name" placeholder="Last name">
                                <span class="field-error" data-error-for="last_name" role="alert"></span>
                                </div>
                            <div class="fr-field">
                                <label for="farmerFirstName">First Name <span class="required">*</span></label>
                                <input type="text" id="farmerFirstName" name="first_name" maxlength="80" autocomplete="given-name" placeholder="First name">
                                <span class="field-error" data-error-for="first_name" role="alert"></span>
                            </div>
                            </div>
                        <div class="fr-row3" style="margin-top:1.5rem;">
                            <div class="fr-field">
                                <label for="province">Province <span class="required">*</span></label>
                                <input type="text" id="province" name="province" value="Batangas" readonly aria-readonly="true">
                            </div>
                            <div class="fr-field">
                                <label for="municipality">Municipality <span class="required">*</span></label>
                                <input type="text" id="municipality" name="municipality" value="Lipa City" readonly aria-readonly="true">
                            </div>
                            <div class="fr-field">
                                <label for="barangay">Barangay <span class="required">*</span></label>
                                <select id="barangay" name="barangay" required>
                                    <option value="">Select barangay</option>
                                    <option value="Adya">Adya</option>
                                    <option value="Antipolo del Sur">Antipolo del Sur</option>
                                    <option value="Bagong Pook">Bagong Pook</option>
                                    <option value="Bulacnin">Bulacnin</option>
                                    <option value="Halang">Halang</option>
                                    <option value="Kayumanggi">Kayumanggi</option>
                                    <option value="Latag">Latag</option>
                                    <option value="Lodlod">Lodlod</option>
                                    <option value="Lumbang">Lumbang</option>
                                    <option value="Malagonlong">Malagonlong</option>
                                    <option value="Malitlit">Malitlit</option>
                                    <option value="Pagolingin">Pagolingin</option>
                                    <option value="Pangao">Pangao</option>
                                    <option value="Pinagkawitan">Pinagkawitan</option>
                                    <option value="Pinagtong-Ulan">Pinagtong-Ulan</option>
                                    <option value="Pusil">Pusil</option>
                                    <option value="Quezon">Quezon</option>
                                    <option value="Rizal">Rizal</option>
                                    <option value="San Benito">San Benito</option>
                                    <option value="San Celestino">San Celestino</option>
                                    <option value="San Isidro">San Isidro</option>
                                    <option value="San Salvador">San Salvador</option>
                                    <option value="Santo Niño">Santo Niño</option>
                                    <option value="Santo Toribio">Santo Toribio</option>
                                    <option value="Talisay">Talisay</option>
                                    <option value="Tangob">Tangob</option>
                                    <option value="Tangway">Tangway</option>
                                    <option value="Tipakan">Tipakan</option>
                                </select>
                                <span class="field-error" data-error-for="barangay" role="alert"></span>
                            </div>
                        </div>
                        
                        <p class="fr-section-title">Affiliation</p>
                        <div class="fr-fed-block">
                            <div class="fr-fed-row" role="group" aria-label="Affiliation">
                                <div class="fr-field">
                                    <label for="frAffiliationRole">Affiliation Role <span class="required">*</span></label>
                                    <select id="frAffiliationRole" name="affiliation_role" aria-label="Affiliation role">
                                        <option value="">Select role</option>
                                        <option value="Cluster Head">Cluster Head</option>
                                        <option value="Officer">Officer</option>
                                        <option value="Member">Member</option>
                                    </select>
                                    <span class="field-error" data-error-for="affiliation_role" role="alert"></span>
                                </div>
                                <div class="fr-field">
                                    <label for="frNcfrs">NCFRS <span class="required">*</span></label>
                                    <select id="frNcfrs" name="ncfrs" aria-label="NCFRS">
                                        <option value="">Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    <span class="field-error" data-error-for="ncfrs" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="fr-row2" style="margin-top:1.5rem;">
                            <div class="fr-field">
                                <label for="frRsbsa">RSBSA Registered <span class="required">*</span></label>
                                <select id="frRsbsa" name="rsbsa_registered">
                                    <option value="">Select</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                    <option value="pending">Pending</option>
                                </select>
                                <span class="field-error" data-error-for="rsbsa_registered" role="alert"></span>
                            </div>
                            <div class="fr-field">
                                <label for="frRsbsaNo">RSBSA Registered Number</label>
                                <input type="text" id="frRsbsaNo" name="rsbsa_number" maxlength="120" placeholder="Number if registered">
                                <span class="field-error" data-error-for="rsbsa_number" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        
                    <div id="frStep2" class="fr-step" data-fr-step="2">
                        <h2 class="fr-page-heading">Farm Information</h2>
                        <div class="fr-field">
                            <label for="frOwnership">Status of Ownership <span class="required">*</span></label>
                            <select id="frOwnership" name="ownership_status">
                                <option value="">Select</option>
                                <option value="landowner">Landowner</option>
                                <option value="cloa_holder">CLOA Holder</option>
                                <option value="list_holder">List Holder</option>
                                <option value="sessional_farm_worker">Sessional Farm Worker</option>
                                <option value="others">Others</option>
                            </select>
                            <span class="field-error" data-error-for="ownership_status" role="alert"></span>
                            </div>
                        <div class="fr-field" style="margin-top:1.6rem;">
                            <label for="plantAreaVal">Total Plant Area <span class="required">*</span></label>
                            <div class="fr-split-area">
                                <input type="number" id="plantAreaVal" name="plant_area_value" min="0" step="any" placeholder="Area">
                                <select id="plantAreaUnit" name="plant_area_unit" aria-label="Area unit">
                                    <option value="">Unit</option>
                                    <option value="ha">Hectares (ha)</option>
                                    <option value="sqm">Square meters (sqm)</option>
                                    <option value="ac">Acres</option>
                                </select>
                            </div>
                            <span class="field-error" data-error-for="plant_area_value" role="alert"></span>
                            <span class="field-error" data-error-for="plant_area_unit" role="alert"></span>
                        </div>
                        
                        <p class="fr-section-title">Tree counts</p>
                        <div class="fr-tree-grid">
                            <div class="fr-field">
                                <label class="fr-tree-label" for="libB">
                                    <span class="fr-tree-variety">Liberica (Kapeng Barako)</span>
                                    <span class="fr-tree-metric">number of bearing</span>
                            </label>
                                <input type="number" id="libB" name="liberica_bearing" min="0" step="1" value="0">
                                <span class="field-error" data-error-for="liberica_bearing" role="alert"></span>
                        </div>
                            <div class="fr-field">
                                <label class="fr-tree-label" for="libN">
                                    <span class="fr-tree-variety">Liberica (Kapeng Barako)</span>
                                    <span class="fr-tree-metric">number of non bearing</span>
                                </label>
                                <input type="number" id="libN" name="liberica_non_bearing" min="0" step="1" value="0">
                                <span class="field-error" data-error-for="liberica_non_bearing" role="alert"></span>
                </div>
                            <div class="fr-field">
                                <label class="fr-tree-label" for="robB">
                                    <span class="fr-tree-variety">Robusta</span>
                                    <span class="fr-tree-metric">number of bearing</span>
                                </label>
                                <input type="number" id="robB" name="robusta_bearing" min="0" step="1" value="0">
                                <span class="field-error" data-error-for="robusta_bearing" role="alert"></span>
            </div>
                            <div class="fr-field">
                                <label class="fr-tree-label" for="robN">
                                    <span class="fr-tree-variety">Robusta</span>
                                    <span class="fr-tree-metric">number of non bearing</span>
                                </label>
                                <input type="number" id="robN" name="robusta_non_bearing" min="0" step="1" value="0">
                                <span class="field-error" data-error-for="robusta_non_bearing" role="alert"></span>
                        </div>
                            <div class="fr-field">
                                <label class="fr-tree-label" for="excB">
                                    <span class="fr-tree-variety">Excelsa</span>
                                    <span class="fr-tree-metric">number of bearing</span>
                                </label>
                                <input type="number" id="excB" name="excelsa_bearing" min="0" step="1" value="0">
                                <span class="field-error" data-error-for="excelsa_bearing" role="alert"></span>
                        </div>
                            <div class="fr-field">
                                <label class="fr-tree-label" for="excN">
                                    <span class="fr-tree-variety">Excelsa</span>
                                    <span class="fr-tree-metric">number of non bearing</span>
                                </label>
                                <input type="number" id="excN" name="excelsa_non_bearing" min="0" step="1" value="0">
                                <span class="field-error" data-error-for="excelsa_non_bearing" role="alert"></span>
                    </div>
                        </div>
                        
                        <p class="fr-section-title">Production</p>
                        <div class="fr-prod-block">
                            <div class="fr-prod-variety">Liberica (Kapeng Barako)</div>
                            <div class="fr-prod-pair">
                                <input type="number" name="liberica_prod_qty" min="0" step="1" inputmode="numeric" placeholder="Quantity">
                                <select name="liberica_prod_unit" aria-label="Liberica unit">
                                    <option value="">Unit</option>
                                    <option value="kg">kg</option>
                                    <option value="sacks">sacks</option>
                                    <option value="tons">tons</option>
                                </select>
                            </div>
                            <span class="field-error" data-error-for="liberica_prod_qty" role="alert"></span>
                            <span class="field-error" data-error-for="liberica_prod_unit" role="alert"></span>
                            </div>
                        <div class="fr-prod-block">
                            <div class="fr-prod-variety">Robusta</div>
                            <div class="fr-prod-pair">
                                <input type="number" name="robusta_prod_qty" min="0" step="1" inputmode="numeric" placeholder="Quantity">
                                <select name="robusta_prod_unit" aria-label="Robusta unit">
                                    <option value="">Unit</option>
                                    <option value="kg">kg</option>
                                    <option value="sacks">sacks</option>
                                    <option value="tons">tons</option>
                                </select>
                        </div>
                            <span class="field-error" data-error-for="robusta_prod_qty" role="alert"></span>
                            <span class="field-error" data-error-for="robusta_prod_unit" role="alert"></span>
                        </div>
                        <div class="fr-prod-block">
                            <div class="fr-prod-variety">Excelsa</div>
                            <div class="fr-prod-pair">
                                <input type="number" name="excelsa_prod_qty" min="0" step="1" inputmode="numeric" placeholder="Quantity">
                                <select name="excelsa_prod_unit" aria-label="Excelsa unit">
                                    <option value="">Unit</option>
                                    <option value="kg">kg</option>
                                    <option value="sacks">sacks</option>
                                    <option value="tons">tons</option>
                                </select>
                            </div>
                            <span class="field-error" data-error-for="excelsa_prod_qty" role="alert"></span>
                            <span class="field-error" data-error-for="excelsa_prod_unit" role="alert"></span>
                            </div>
                        </div>
                        
                    <div id="frStep3" class="fr-step" data-fr-step="3">
                        <p class="fr-section-title">Profile Picture</p>
                        <div class="fr-photo-step">
                            <div id="frPhotoToast" class="fr-photo-toast" role="status" aria-live="polite" aria-atomic="true">
                                <i data-lucide="alert-circle" class="toast-icon" aria-hidden="true"></i>
                                <div id="frPhotoToastText" class="toast-text"></div>
                            </div>
                            <div class="fr-photo-avatar-wrap">
                                <img id="frProfilePreview" class="fr-photo-avatar" src="" alt="Profile preview" style="display:none;">
                                <button type="button" class="fr-photo-icon-btn" id="frTakePictureIcon" aria-label="Take picture">
                                    <i data-lucide="camera"></i>
                                </button>
                        </div>
                            <div class="fr-photo-actions">
                                <button type="button" class="btn-fr-photo take" id="frTakePictureBtn">Take Picture</button>
                                <button type="button" class="btn-fr-photo upload" id="frUploadPictureBtn">Upload Picture</button>
                        </div>
                            <input type="file" id="frTakePictureInput" accept="image/*" class="fr-visually-hidden-file" aria-hidden="true" tabindex="-1">
                            <input type="file" id="frUploadPictureInput" accept="image/*" class="fr-visually-hidden-file" aria-hidden="true" tabindex="-1">
                            <input type="hidden" id="frPhotoData" name="profile_photo_data">
                            <p class="fr-photo-hint">Use camera or gallery to add your profile photo.</p>
                            <span class="field-error" data-error-for="profile_photo_data" role="alert"></span>
                        </div>
                        <div class="fr-camera-modal" id="frCameraModal" aria-hidden="true">
                            <div class="fr-camera-panel" role="dialog" aria-label="Take profile photo">
                                <video id="frCameraVideo" class="fr-camera-video" autoplay playsinline muted></video>
                                <canvas id="frCameraCanvas" style="display:none;"></canvas>
                                <div class="fr-camera-actions">
                                    <button type="button" class="btn-fr-cam" id="frCameraCancelBtn">Cancel</button>
                                    <button type="button" class="btn-fr-cam capture" id="frCameraCaptureBtn">Capture</button>
                                </div>
                            </div>
                        </div>
                        
                        </div>
                        
                    <div id="frStep4" class="fr-step" data-fr-step="4">
                        <h2 class="fr-terms-title">Terms &amp; Conditions</h2>
                        <p class="fr-terms-body">
                            Welcome to Beanthentic, a digital application designed to facilitate the registration and management
                            of coffee farms in Lipa City. By accessing, registering, or using the application, you acknowledge
                            that you have read, understood, and agreed to be bound by these terms and conditions. If you do not
                            agree with any part of these terms, you must discontinue use of the application immediately. Beanthentic
                            provides services including the collection, storage, and management of personal and farm-related
                            information to support agricultural programs and organization activities. By using the application, you
                            represent that you are at least eighteen (18) years of age or have obtained consent from a parent or
                            legal guardian, have the legal capacity to enter into this agreement, and agree that all information you
                            provide is accurate and complete. You also agree that you will not misrepresent your identity or submit
                            false or misleading data. You are responsible for maintaining the confidentiality of your account
                            credentials and for all activities conducted under your account. Beanthentic shall not be liable for any
                            loss or damage resulting from failure to protect your login information. You agree to notify us immediately
                            of any unauthorized access or suspected security breach involving your account. The application reserves
                            the right to suspend or terminate accounts involved in violations, fraud, or unlawful activities.
                        </p>
                        <div class="form-group" style="margin-top:1.25rem;">
                            <label class="checkbox-label" for="agreeRegistration">
                                <input type="checkbox" id="agreeRegistration" name="agree_registration" value="yes">
                                <span>I certify that my registration details are <strong>true and complete</strong> to the best of my knowledge, and I understand false information may affect Register Farm eligibility.</span>
                            </label>
                            <span class="field-error" data-error-for="agree_registration" role="alert"></span>
                </div>
            </div>
            
                    <div class="fr-wizard-foot fr-wizard-foot--first" id="frWizardFoot">
                        <button type="button" class="btn-fr-back" id="frWizardBack" aria-label="Previous step">← Back</button>
                        <div style="display:flex; gap:0.55rem; margin-left:auto;">
                            <button type="button" class="btn-fr-next" id="frWizardNext">Continue →</button>
                            <button type="submit" class="btn-fr-next btn-fr-submit" id="frWizardSubmit" aria-label="Submit registration">Submit →</button>
                        </div>
                        </div>
                </form>
                <div class="fr-submit-overlay loading" id="frLoadingOverlay" aria-hidden="true">
                    <div class="fr-loading-card">
                        <span class="fr-loading-bean" aria-hidden="true"><img src="coffee_bean_loading.png" alt="" /></span>
                        <span class="fr-loading-text">Please wait for a moment</span>
                    </div>
                        </div>
                <div class="fr-submit-overlay success" id="frSuccessOverlay" aria-hidden="true">
                    <div class="fr-success-mark"><i data-lucide="check"></i></div>
                    <p class="fr-success-title">Success!</p>
                    <p class="fr-success-subtitle">Registration Completed</p>
                    <div class="fr-success-actions">
                        <button type="button" class="fr-success-btn primary" id="frSuccessViewSummary">View Summary</button>
                        <button type="button" class="fr-success-btn" id="frSuccessGoHome">Go Home</button>
                    </div>
                </div>
                <div class="fr-submit-overlay block" id="frAlreadyOverlay" aria-hidden="true">
                    <div class="fr-block-card">
                        <p class="fr-block-title">Already Registered</p>
                        <p class="fr-block-body">This account/device already has a completed Farmer Registration. Only new accounts can register.</p>
                        <div class="fr-block-actions">
                            <button type="button" class="fr-block-btn primary" id="frAlreadyGoHome">Go to Home</button>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    <script>
        let farmerId = null;
        const EMAIL_RE = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$/;

        function refreshIcons(root) {
            if (window.lucide) lucide.createIcons({ attrs: { 'stroke-width': 2 }, nameAttr: 'data-lucide', root: root || document.body });
        }

        function clearErrors(scope) {
            const el = scope || document;
            el.querySelectorAll('.field-error').forEach(s => { s.textContent = ''; });
            el.querySelectorAll('.input-invalid').forEach(i => i.classList.remove('input-invalid'));
            el.querySelectorAll('.checkbox-invalid').forEach(l => l.classList.remove('checkbox-invalid'));
        }

        function setFieldError(fieldKey, message) {
            const span = document.querySelector('.field-error[data-error-for="' + fieldKey + '"]');
            if (span) span.textContent = message || '';
            const input = document.querySelector('[name="' + fieldKey + '"]');
            if (input && message) {
                input.classList.add('input-invalid');
                const lab = input.closest('.checkbox-label');
                if (lab) lab.classList.add('checkbox-invalid');
            } else if (input && !message) {
                input.classList.remove('input-invalid');
                const lab = input.closest('.checkbox-label');
                if (lab) lab.classList.remove('checkbox-invalid');
            }
        }

        function applyServerErrors(errors) {
            if (!errors) return;
            Object.keys(errors).forEach(k => {
                if (k === '_error') {
                    showAlert(errors[k], 'error');
                    return;
                }
                setFieldError(k, errors[k]);
            });
        }

        function moveWizardToStepForErrors(errors) {
            if (!errors) return;
            const keys = Object.keys(errors);
            const stepKeys1 = ['last_name','first_name','barangay','affiliation_role','ncfrs','rsbsa_registered','rsbsa_number'];
            const stepKeys2 = [
                'ownership_status','plant_area_value','plant_area_unit',
                'liberica_bearing','liberica_non_bearing','robusta_bearing','robusta_non_bearing','excelsa_bearing','excelsa_non_bearing',
                'liberica_prod_qty','liberica_prod_unit','robusta_prod_qty','robusta_prod_unit','excelsa_prod_qty','excelsa_prod_unit'
            ];
            const stepKeys3 = ['profile_photo_data'];
            const stepKeys4 = ['agree_registration'];
            if (keys.some(k => stepKeys1.indexOf(k) >= 0)) frStep = 1;
            else if (keys.some(k => stepKeys2.indexOf(k) >= 0)) frStep = 2;
            else if (keys.some(k => stepKeys3.indexOf(k) >= 0)) frStep = 3;
            else if (keys.some(k => stepKeys4.indexOf(k) >= 0)) frStep = 4;
            syncFrWizardUi();
        }

        function normalizePhone(v) {
            if (!v) return '';
            let s = String(v).replace(/[\\s\\-]/g, '');
            if (s.startsWith('+63')) s = '0' + s.slice(3);
            else if (s.startsWith('63') && s.length >= 11) s = '0' + s.slice(2);
            return s;
        }

        function looksLikePersonName(value) {
            const s = String(value || '').trim();
            if (!s || s.length < 2) return false;
            // Prevent numeric identifiers (e.g. phone/account number) from entering name fields.
            if (/^[0-9]+$/.test(s)) return false;
            return true;
        }

        const LIPA_BARANGAYS_REGISTER = new Set([
                'Adya','Antipolo del Sur','Bagong Pook','Bulacnin','Halang','Kayumanggi','Latag','Lodlod','Lumbang',
                'Malagonlong','Malitlit','Pagolingin','Pangao','Pinagkawitan','Pinagtong-Ulan','Pusil','Quezon','Rizal',
                'San Benito','San Celestino','San Isidro','San Salvador','Santo Niño','Santo Toribio','Talisay','Tangob',
                'Tangway','Tipakan'
            ]);

        function validateStepPersonalErrors(d) {
            const err = {};
            const last = (d.last_name || '').trim();
            const first = (d.first_name || '').trim();
            const barangay = (d.barangay || '').trim();
            if (last.length < 2) err.last_name = 'Enter your last name.';
            if (first.length < 2) err.first_name = 'Enter your first name.';
            if (!barangay) err.barangay = 'Select your barangay in Lipa City.';
            else if (!LIPA_BARANGAYS_REGISTER.has(barangay)) err.barangay = 'Barangay must be within Lipa City.';
            return err;
        }

        function validateStepAffiliationErrors(d) {
            const err = {};
            const role = (d.affiliation_role || '').trim();
            const ncfrs = (d.ncfrs || '').trim().toLowerCase();
            const rsb = (d.rsbsa_registered || '').trim().toLowerCase();
            const rsbNum = (d.rsbsa_number || '').trim();
            if (!role) err.affiliation_role = 'Select your role.';
            if (!['yes','no'].includes(ncfrs)) err.ncfrs = 'Select NCFRS (Yes or No).';
            if (!['yes','no','pending'].includes(rsb)) err.rsbsa_registered = 'Select RSBSA registration status.';
            if (rsb === 'yes' && rsbNum.length < 4) err.rsbsa_number = 'Enter your RSBSA number.';
            return err;
        }

        function validateContactErrors(d) {
            const err = {};
            const email = (d.email || '').trim().toLowerCase();
            const phone = normalizePhone(d.phone);
            if (!EMAIL_RE.test(email)) err.email = 'Enter a valid email address.';
            if (!phone) err.phone = 'Mobile number is required (09XXXXXXXXX).';
            else if (!/^09\\d{9}$/.test(phone)) err.phone = 'Use Philippine mobile format: 09XXXXXXXXX.';
            return err;
        }

        function validateStep2Errors(d) {
            const err = {};
            const own = (d.ownership_status || '').trim().toLowerCase();
            const okOwn = new Set(['landowner','cloa_holder','list_holder','sessional_farm_worker','others']);
            if (!okOwn.has(own)) err.ownership_status = 'Select status of ownership.';
            const unit = (d.plant_area_unit || '').trim().toLowerCase();
            if (!['ha','sqm','ac'].includes(unit)) err.plant_area_unit = 'Select a unit.';
            const rawA = (d.plant_area_value !== undefined && d.plant_area_value !== null) ? String(d.plant_area_value).trim() : '';
            if (!rawA) err.plant_area_value = 'Enter total plant area.';
            else {
                const a = parseFloat(rawA);
                if (!(a > 0) || isNaN(a)) err.plant_area_value = 'Enter an area greater than zero.';
                else if (a > 1e6) err.plant_area_value = 'Value is too large.';
            }
            ['liberica','robusta','excelsa'].forEach(function (v) {
                const bk = v + '_bearing';
                const nk = v + '_non_bearing';
                [bk, nk].forEach(function (key) {
                    const raw = (d[key] !== undefined && d[key] !== null) ? String(d[key]).trim() : '';
                    if (raw === '') return;
                    const n = parseInt(raw, 10);
                    if (isNaN(n) || n < 0) err[key] = 'Use a whole number ≥ 0.';
                });
            });
            return err;
        }

        function validateStepFinalErrors(d) {
            const err = {};
            if (d.agree_registration !== 'yes') err.agree_registration = 'Please confirm the declaration before submitting.';
            return err;
        }

        function validatePhotoStepErrors(d) {
            const err = {};
            const photo = String(d.profile_photo_data || '').trim();
            if (!photo) err.profile_photo_data = 'Please take or upload a profile photo.';
            return err;
        }

        function validateStepPersonalAndAffiliation(d) {
            return Object.assign({}, validateStepPersonalErrors(d), validateStepAffiliationErrors(d));
        }

        let frStep = 1;
        const frMaxStep = 4;
        function syncFrWizardUi() {
            document.querySelectorAll('.fr-step').forEach(function (el) {
                const step = parseInt(el.getAttribute('data-fr-step'), 10);
                el.classList.toggle('is-active', step === frStep);
            });
            if (frStep !== 3) stopCameraStream();
            const foot = document.getElementById('frWizardFoot');
            const back = document.getElementById('frWizardBack');
            const next = document.getElementById('frWizardNext');
            const sub = document.getElementById('frWizardSubmit');
            if (foot) foot.classList.toggle('fr-wizard-foot--first', frStep === 1);
            if (back) back.classList.toggle('is-visible', frStep > 1);
            if (next) next.style.display = frStep === frMaxStep ? 'none' : '';
            if (sub) sub.classList.toggle('is-visible', frStep === frMaxStep);
        }

        const REGISTER_FARM_REG_OK = 'register_farm_registration_ok';
        const REGISTER_FARM_FARMER_ID = 'register_farm_farmer_id';

        function phpApiBase() {
            try {
                var s = localStorage.getItem('beanthentic_api_base') || sessionStorage.getItem('beanthentic_api_base');
                if (s && String(s).replace(/\\s/g, '')) {
                    return String(s).replace(/\\/$/, '');
                }
            } catch (_e) {}
            try {
                if (typeof location !== 'undefined' && (location.protocol === 'http:' || location.protocol === 'https:')) {
                    var base = new URL('.', location.href).href;
                    return String(base || '').replace(/\\/+$/, '');
                }
            } catch (_e2) {}
            return '';
        }

        function syncRegisterNavIconFromStorage() {
            var nav = document.getElementById('nav-register');
            if (!nav) return;
            var done = false;
            try {
                function parseUser(raw) {
                    if (!raw) return null;
                    try {
                        var u = JSON.parse(raw);
                        return (u && u.email) ? u : null;
                    } catch (_e) {
                        return null;
                    }
                }
                function keyVariants(v) {
                    var out = [];
                    var k = String(v || '').trim().toLowerCase();
                    if (!k) return out;
                    out.push(k);
                    var d = k.replace(/\\D/g, '');
                    if (d) {
                        if (d.indexOf('63') === 0 && d.length >= 12) out.push('0' + d.slice(2));
                        if (d.indexOf('0') === 0 && d.length >= 11) out.push('+63' + d.slice(1));
                        if (d.length === 10 && d.charAt(0) === '9') {
                            out.push('0' + d);
                            out.push('+63' + d);
                        }
                    }
                    return Array.from(new Set(out));
                }
                var u = parseUser(localStorage.getItem('beanthentic_user')) || parseUser(sessionStorage.getItem('beanthentic_user'));
                var key = u && u.email ? String(u.email).trim().toLowerCase() : '';
                if (key) {
                    var rawMap = localStorage.getItem('beanthentic_farmer_id_map') || sessionStorage.getItem('beanthentic_farmer_id_map');
                    var map = rawMap ? JSON.parse(rawMap) : null;
                    if (map && typeof map === 'object') {
                        var keys = keyVariants(key);
                        for (var i = 0; i < keys.length; i += 1) {
                            var idv = map[keys[i]];
                            if (idv != null && String(idv).trim() !== '') {
                                done = true;
                                break;
                            }
                        }
                    }
                }
            } catch (_e) {}
            if (!done) {
                try {
                    var id = localStorage.getItem('beanthentic_farmer_id');
                    done = !!(id && String(id).trim());
                } catch (_e2) {}
            }
            nav.classList.toggle('is-register-complete', done);
        }
        window.beanthenticSyncRegisterNavIcon = syncRegisterNavIconFromStorage;

        (function syncRegistrationSessionFromStorage() {
            try {
                var id = localStorage.getItem('beanthentic_farmer_id');
                if (id && String(id).trim()) {
                    sessionStorage.setItem(REGISTER_FARM_REG_OK, '1');
                    sessionStorage.setItem(REGISTER_FARM_FARMER_ID, String(id).trim());
                }
            } catch (_e) {}
        })();

        function setRegisterButtonLoading(btn, isLoading) {
            if (!btn) return;
            if (isLoading) {
                if (btn.dataset.loading === '1') return;
                btn.dataset.loading = '1';
                btn.dataset.loadingSince = String(Date.now());
                btn.dataset.originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.classList.add('is-loading');
                btn.setAttribute('aria-busy', 'true');
                btn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span><span>Registering...</span>';
                return;
            }
            if (btn.dataset.loading !== '1') return;
            btn.dataset.loading = '0';
            btn.disabled = false;
            btn.classList.remove('is-loading');
            btn.setAttribute('aria-busy', 'false');
            if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
            refreshIcons(btn);
        }

        function endRegisterButtonLoading(btn) {
            if (!btn) return;
            const started = parseInt(btn.dataset.loadingSince || '0', 10);
            const elapsed = Math.max(0, Date.now() - (isNaN(started) ? 0 : started));
            const minVisibleMs = 2000;
            const delay = Math.max(0, minVisibleMs - elapsed);
            setTimeout(function () {
                setRegisterButtonLoading(btn, false);
            }, delay);
        }

        function hasCompletedRegistration() {
            try {
                function parseUser(raw) {
                    if (!raw) return null;
                    try {
                        var u = JSON.parse(raw);
                        return (u && u.email) ? u : null;
                    } catch (_e) {
                        return null;
                    }
                }
                var u = parseUser(localStorage.getItem('beanthentic_user')) || parseUser(sessionStorage.getItem('beanthentic_user'));
                var email = u && u.email ? String(u.email).trim().toLowerCase() : '';
                if (!email) return false;
                var rawMap = localStorage.getItem('beanthentic_farmer_id_map') || sessionStorage.getItem('beanthentic_farmer_id_map');
                var map = rawMap ? JSON.parse(rawMap) : null;
                if (map && typeof map === 'object' && typeof map[email] === 'string' && map[email].trim()) return true;
            } catch (_e) {}
            return false;
        }

        function showAlreadyRegisteredScreen() {
            try {
                const hero = document.querySelector('.fr-reg-hero');
                if (hero) hero.style.display = 'none';
                const sheet = document.querySelector('.register-farm-sheet');
                if (sheet) sheet.style.display = 'none';
                const bottomNav = document.querySelector('.app-bottom-nav');
                if (bottomNav) bottomNav.style.display = 'none';
            } catch (_e) {}
            toggleSubmitOverlay('already', true);
        }

        (function prefillFromUrl() {
            try {
                const p = new URLSearchParams(window.location.search || '');
                const n = (p.get('name') || '').trim();
                const e = (p.get('email') || '').trim();
                if (n && looksLikePersonName(n)) {
                    const parts = n.split(/\\s+/);
                    const fn = document.getElementById('farmerFirstName');
                    const ln = document.getElementById('farmerLastName');
                    if (parts.length >= 2 && fn && ln && !fn.value.trim() && !ln.value.trim()) {
                        ln.value = parts[parts.length - 1];
                        fn.value = parts.slice(0, -1).join(' ');
                    } else if (fn && !fn.value.trim()) {
                        fn.value = n;
                    }
                }
                if (e) {
                    const el = document.getElementById('farmerEmail');
                    if (el && !el.value.trim()) el.value = e;
                }
            } catch (_err) {}
        })();

        (function prefillFromAccount() {
            try {
                function parseUser(raw) {
                    if (!raw) return null;
                    try {
                        const u = JSON.parse(raw);
                        if (u && u.email) return u;
                    } catch (_e) {}
                    return null;
                }
                const u = parseUser(localStorage.getItem('beanthentic_user')) || parseUser(sessionStorage.getItem('beanthentic_user'));
                if (!u) return;
                const id = String(u.email || '').trim();
                const em = document.getElementById('farmerEmail');
                const ph = document.getElementById('farmerPhone');
                if (EMAIL_RE.test(id)) {
                    if (em && !em.value.trim()) em.value = id;
                } else {
                    const n = normalizePhone(id);
                    if (ph && !ph.value.trim() && /^09\\d{9}$/.test(n)) ph.value = n;
                }
                const fn = document.getElementById('farmerFirstName');
                const ln = document.getElementById('farmerLastName');
                if (looksLikePersonName(u.name) && fn && ln && !fn.value.trim() && !ln.value.trim()) {
                    const parts = String(u.name).trim().split(/\\s+/);
                    if (parts.length >= 2) {
                        ln.value = parts[parts.length - 1];
                        fn.value = parts.slice(0, -1).join(' ');
                    } else {
                        fn.value = String(u.name);
                    }
                }
            } catch (_e2) {}
        })();

        const frForm = document.getElementById('farmerForm');
        const frNext = document.getElementById('frWizardNext');
        const frBack = document.getElementById('frWizardBack');
        const frTakePictureBtn = document.getElementById('frTakePictureBtn');
        const frUploadPictureBtn = document.getElementById('frUploadPictureBtn');
        const frTakePictureIcon = document.getElementById('frTakePictureIcon');
        const frTakePictureInput = document.getElementById('frTakePictureInput');
        const frUploadPictureInput = document.getElementById('frUploadPictureInput');
        const frProfilePreview = document.getElementById('frProfilePreview');
        const frPhotoData = document.getElementById('frPhotoData');
        const frHeroProfilePhoto = document.getElementById('frHeroProfilePhoto');
        const frHeroAvatar = document.getElementById('frHeroAvatar');
        const frHeroPickPhotoBtn = document.getElementById('frHeroPickPhotoBtn');
        const frCameraModal = document.getElementById('frCameraModal');
        const frCameraVideo = document.getElementById('frCameraVideo');
        const frCameraCanvas = document.getElementById('frCameraCanvas');
        const frCameraCaptureBtn = document.getElementById('frCameraCaptureBtn');
        const frCameraCancelBtn = document.getElementById('frCameraCancelBtn');
        const frLoadingOverlay = document.getElementById('frLoadingOverlay');
        const frSuccessOverlay = document.getElementById('frSuccessOverlay');
        const frAlreadyOverlay = document.getElementById('frAlreadyOverlay');
        const frAlreadyGoHome = document.getElementById('frAlreadyGoHome');
        const frSuccessViewSummary = document.getElementById('frSuccessViewSummary');
        const frSuccessGoHome = document.getElementById('frSuccessGoHome');
        let frCameraStream = null;
        let frPhotoToastTimer = null;

        function goRegisterSummary() {
            try {
                // Prefer relative page so this works on localhost/192/file WebView contexts.
                window.location.assign('register_summary.php');
            } catch (_e) {
                try { window.location.assign('/register_summary.php'); } catch (_e2) {}
            }
        }

        if (frAlreadyGoHome) {
            frAlreadyGoHome.addEventListener('click', function () {
                window.location.assign('/#home');
            });
        }
        if (frSuccessViewSummary) {
            frSuccessViewSummary.addEventListener('click', function () {
                goRegisterSummary();
            });
        }
        if (frSuccessGoHome) {
            frSuccessGoHome.addEventListener('click', function () {
                window.location.assign('/#home');
            });
        }

        // Keep registration form visible. Home flow already controls who should register.
        // (Avoid hiding the whole form due stale/shared localStorage state.)

        function showPhotoToast(message, type) {
            const toast = document.getElementById('frPhotoToast');
            const text = document.getElementById('frPhotoToastText');
            if (!toast || !text) {
                showAlert(message, type || 'error');
                return;
            }
            if (frPhotoToastTimer) {
                clearTimeout(frPhotoToastTimer);
                frPhotoToastTimer = null;
            }
            toast.classList.toggle('success', type === 'success');
            toast.classList.add('is-open');
            text.textContent = message || '';
            refreshIcons(toast);
            frPhotoToastTimer = setTimeout(function () {
                toast.classList.remove('is-open');
            }, 4200);
        }

        function stopCameraStream() {
            if (frCameraStream) {
                frCameraStream.getTracks().forEach(function (t) { t.stop(); });
                frCameraStream = null;
            }
            if (frCameraVideo) frCameraVideo.srcObject = null;
            if (frCameraModal) {
                frCameraModal.classList.remove('is-open');
                frCameraModal.setAttribute('aria-hidden', 'true');
            }
        }

        function ensureGetUserMediaPolyfill() {
            try {
                if (typeof navigator === 'undefined') return;
                if (!navigator.mediaDevices) {
                    navigator.mediaDevices = {};
                }
                if (navigator.mediaDevices.getUserMedia) return;
                var legacy = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
                if (!legacy) return;
                navigator.mediaDevices.getUserMedia = function (constraints) {
                    return new Promise(function (resolve, reject) {
                        legacy.call(navigator, constraints, resolve, reject);
                    });
                };
            } catch (_p) {}
        }

        function isLikelyMobileOrTablet() {
            try {
                var ua = String((navigator && navigator.userAgent) || '');
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua)) return true;
                if (navigator.maxTouchPoints > 1 && /Macintosh/i.test(ua)) return true;
                if (navigator.maxTouchPoints > 0 && window.matchMedia && window.matchMedia('(pointer: coarse)').matches) return true;
            } catch (_m) {}
            return false;
        }

        /**
         * captureMode: 'environment' (back), 'user' (front), 'file' (no capture — gallery/files only)
         * Must run in the same synchronous user gesture as the button tap (no await before this).
         */
        function openTakePictureFilePicker(captureMode) {
            if (!frTakePictureInput) return false;
            try { frTakePictureInput.value = ''; } catch (_e0) {}
            try {
                frTakePictureInput.removeAttribute('capture');
                if (captureMode === 'environment') {
                    frTakePictureInput.setAttribute('capture', 'environment');
                } else if (captureMode === 'user') {
                    frTakePictureInput.setAttribute('capture', 'user');
                }
            } catch (_attr) {}
            try {
                frTakePictureInput.click();
                return true;
            } catch (_e2) {
                return false;
            }
        }

        function openTakePictureInputFallback() {
            var mobile = isLikelyMobileOrTablet();
            if (mobile) {
                if (openTakePictureFilePicker('environment')) return true;
                if (openTakePictureFilePicker('user')) return true;
            }
            if (openTakePictureFilePicker('file')) return true;
            return false;
        }

        /** Live preview only when secure context; uses promises (no async on button handler). */
        function startLiveCameraPreview() {
            if (!frCameraModal || !frCameraVideo) {
                openTakePictureInputFallback();
                return;
            }
            if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                openTakePictureInputFallback();
                return;
            }
            var tries = [
                { video: true, audio: false },
                { video: { facingMode: 'user' }, audio: false },
                { video: { facingMode: 'environment' }, audio: false },
                { video: { facingMode: { ideal: 'user' } }, audio: false },
                { video: { facingMode: { ideal: 'environment' } }, audio: false }
            ];
            function attempt(idx) {
                if (idx >= tries.length) {
                    requestAnimationFrame(function () {
                        showPhotoToast('Live preview unavailable — use Upload Picture or try again.', 'error');
                    });
                    return;
                }
                navigator.mediaDevices.getUserMedia(tries[idx]).then(function (stream) {
                    frCameraStream = stream;
                    frCameraVideo.srcObject = stream;
                    frCameraModal.classList.add('is-open');
                    frCameraModal.setAttribute('aria-hidden', 'false');
                    var p = frCameraVideo.play();
                    if (p && typeof p.then === 'function') {
                        p.catch(function () {});
                    }
                }).catch(function () {
                    attempt(idx + 1);
                });
            }
            attempt(0);
        }

        /**
         * SYNCHRONOUS entry: opens native camera/file sheet in the same gesture as the click.
         * Browsers block delayed input.click() after await / microtasks without a fresh tap.
         */
        function openDeviceCamera() {
            ensureGetUserMediaPolyfill();

            var mobile = isLikelyMobileOrTablet();
            var insecure = false;
            try {
                insecure = typeof window !== 'undefined' && window.isSecureContext === false;
            } catch (_s) {}

            if (!frCameraModal || !frCameraVideo) {
                openTakePictureInputFallback();
                return;
            }

            if (mobile) {
                if (openTakePictureFilePicker('environment') || openTakePictureFilePicker('user')) {
                    return;
                }
            }

            // Laptop on http://LAN: getUserMedia is blocked — open OS picker immediately (same tap).
            if (!mobile && insecure) {
                if (openTakePictureFilePicker('user') || openTakePictureFilePicker('environment') || openTakePictureFilePicker('file')) {
                    return;
                }
            }

            // localhost / https: try in-page live preview (async inside startLiveCameraPreview, gesture only needed for gUM prompt).
            if (!insecure && navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function') {
                startLiveCameraPreview();
                return;
            }

            if (openTakePictureInputFallback()) {
                return;
            }
            requestAnimationFrame(function () {
                showPhotoToast('Cannot open camera. Tap Upload Picture below.', 'error');
            });
        }

        /** Same user-gesture path as Take Picture — opens native sheet (camera + gallery on many phones). */
        function openProfilePhotoPickerSameGesture() {
            if (!frTakePictureInput) {
                openDeviceCamera();
                return;
            }
            try { frTakePictureInput.value = ''; } catch (_e0) {}
            try { frTakePictureInput.removeAttribute('capture'); } catch (_e1) {}
            try {
                frTakePictureInput.click();
            } catch (_e2) {
                openDeviceCamera();
            }
        }

        function toggleSubmitOverlay(kind, isVisible) {
            if (kind === 'loading' && frLoadingOverlay) {
                frLoadingOverlay.classList.toggle('is-visible', !!isVisible);
                frLoadingOverlay.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
                refreshIcons(frLoadingOverlay);
            }
            if (kind === 'success' && frSuccessOverlay) {
                frSuccessOverlay.classList.toggle('is-visible', !!isVisible);
                frSuccessOverlay.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
                refreshIcons(frSuccessOverlay);
            }
            if (kind === 'already' && frAlreadyOverlay) {
                frAlreadyOverlay.classList.toggle('is-visible', !!isVisible);
                frAlreadyOverlay.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
                refreshIcons(frAlreadyOverlay);
            }
        }

        function looksLikeImageFile(file) {
            if (!file) return false;
            const t = String(file.type || '').toLowerCase();
            if (t.indexOf('image/') === 0) return true;
            const n = String(file.name || '').toLowerCase();
            if (/\\.(jpe?g|png|gif|webp|heic|heif|bmp)$/i.test(n)) return true;
            const sz = Number(file.size || 0);
            if (!t && sz > 512 && sz < 30 * 1024 * 1024) return true;
            return false;
        }

        function syncProfilePhotoToPreviews(dataUrl) {
            const url = String(dataUrl || '').trim();
            if (!url) return;
            if (!/^data:image\\//i.test(url) && !/^https?:\\/\\//i.test(url)) return;
            if (frPhotoData) frPhotoData.value = url;
            if (frProfilePreview) {
                frProfilePreview.src = url;
                frProfilePreview.style.display = 'block';
            }
            if (frHeroProfilePhoto && frHeroAvatar) {
                frHeroProfilePhoto.src = url;
                frHeroProfilePhoto.style.opacity = '1';
                frHeroProfilePhoto.style.visibility = 'visible';
                frHeroAvatar.classList.add('has-photo');
            }
            setFieldError('profile_photo_data', '');
        }

        function bindProfilePhotoInput(inputEl) {
            if (!inputEl) return;
            inputEl.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                if (!looksLikeImageFile(file)) {
                    setFieldError('profile_photo_data', 'Please select an image file.');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (ev) {
                    const result = String((ev && ev.target && ev.target.result) || '');
                    if (!result) return;
                    syncProfilePhotoToPreviews(result);
                };
                reader.readAsDataURL(file);
                this.value = '';
            });
        }

        if (frTakePictureBtn) frTakePictureBtn.addEventListener('click', function () { openDeviceCamera(); });
        if (frTakePictureIcon) frTakePictureIcon.addEventListener('click', function () { openDeviceCamera(); });
        if (frHeroPickPhotoBtn) frHeroPickPhotoBtn.addEventListener('click', function () { openProfilePhotoPickerSameGesture(); });
        if (frUploadPictureBtn && frUploadPictureInput) frUploadPictureBtn.addEventListener('click', function () { frUploadPictureInput.click(); });
        if (frCameraCancelBtn) frCameraCancelBtn.addEventListener('click', function () { stopCameraStream(); });
        if (frCameraCaptureBtn && frCameraVideo && frCameraCanvas) {
            frCameraCaptureBtn.addEventListener('click', function () {
                const w = frCameraVideo.videoWidth || 0;
                const h = frCameraVideo.videoHeight || 0;
                if (!(w > 0 && h > 0)) return;
                frCameraCanvas.width = w;
                frCameraCanvas.height = h;
                const ctx = frCameraCanvas.getContext('2d');
                if (!ctx) return;
                ctx.drawImage(frCameraVideo, 0, 0, w, h);
                const dataUrl = frCameraCanvas.toDataURL('image/jpeg', 0.92);
                syncProfilePhotoToPreviews(dataUrl);
                stopCameraStream();
            });
        }
        bindProfilePhotoInput(frTakePictureInput);
        bindProfilePhotoInput(frUploadPictureInput);

        (function restoreDraftProfilePhoto() {
            try {
                const raw = frPhotoData && frPhotoData.value ? String(frPhotoData.value).trim() : '';
                if (raw) syncProfilePhotoToPreviews(raw);
            } catch (_eR) {}
        })();

        (function bindProductionQtyNumericOnly() {
            const qtyNames = ['liberica_prod_qty', 'robusta_prod_qty', 'excelsa_prod_qty'];
            qtyNames.forEach(function (name) {
                const input = document.querySelector('[name="' + name + '"]');
                if (!input) return;
                input.addEventListener('input', function () {
                    this.value = String(this.value || '').replace(/[^0-9]/g, '');
                });
            });
        })();

        function deriveContactFallback() {
            let email = '';
            let phone = '';
            try {
                function parseUser(raw) {
                    if (!raw) return null;
                    try {
                        const u = JSON.parse(raw);
                        if (u) return u;
                    } catch (_e) {}
                    return null;
                }
                const u = parseUser(localStorage.getItem('beanthentic_user')) || parseUser(sessionStorage.getItem('beanthentic_user'));
                const id = String((u && (u.phone_number || u.email)) || '').trim();
                if (EMAIL_RE.test(id)) email = id.toLowerCase();
                const p = normalizePhone(id);
                if (/^09\\d{9}$/.test(p)) phone = p;
            } catch (_e2) {}
            // Keep submit unblocked even when account payload is missing.
            if (!EMAIL_RE.test(email)) email = 'farmer.' + Date.now() + '@beanthentic.local';
            if (!/^09\\d{9}$/.test(phone)) phone = '09999999999';
            return { email: email, phone: phone };
        }

        function collectWizardPayload(fd) {
            const data = Object.fromEntries(fd.entries());
            const contactFallback = deriveContactFallback();
            const rawEmail = (data.email || '').trim().toLowerCase();
            const rawPhone = normalizePhone(data.phone);
            const base = {
                last_name: (data.last_name || '').trim(),
                first_name: (data.first_name || '').trim(),
                email: EMAIL_RE.test(rawEmail) ? rawEmail : contactFallback.email,
                phone: /^09\\d{9}$/.test(rawPhone) ? rawPhone : contactFallback.phone,
                barangay: data.barangay,
                affiliation_role: (data.affiliation_role || '').trim(),
                ncfrs: (data.ncfrs || '').trim().toLowerCase(),
                // Backward-compatible keys for existing server/client code + summary page.
                federation: (data.affiliation_role || '').trim(),
                association: '',
                rsbsa_registered: (data.rsbsa_registered || '').trim(),
                rsbsa_number: (data.rsbsa_number || '').trim(),
                ownership_status: (data.ownership_status || '').trim(),
                plant_area_value: data.plant_area_value,
                plant_area_unit: (data.plant_area_unit || '').trim(),
                liberica_bearing: data.liberica_bearing,
                liberica_non_bearing: data.liberica_non_bearing,
                robusta_bearing: data.robusta_bearing,
                robusta_non_bearing: data.robusta_non_bearing,
                excelsa_bearing: data.excelsa_bearing,
                excelsa_non_bearing: data.excelsa_non_bearing,
                liberica_prod_qty: data.liberica_prod_qty,
                liberica_prod_unit: (data.liberica_prod_unit || '').trim(),
                robusta_prod_qty: data.robusta_prod_qty,
                robusta_prod_unit: (data.robusta_prod_unit || '').trim(),
                excelsa_prod_qty: data.excelsa_prod_qty,
                excelsa_prod_unit: (data.excelsa_prod_unit || '').trim(),
                production_year: (document.getElementById('productionYear') || {}).value || '2026',
                profile_photo_data: (data.profile_photo_data || '').trim(),
                agree_registration: data.agree_registration === 'yes' ? 'yes' : ''
            };
            return base;
        }

        if (frNext) {
            frNext.addEventListener('click', function () {
                if (!frForm) return;
                const fd = new FormData(frForm);
                const data = Object.fromEntries(fd.entries());
                clearErrors(frForm);
                let ve = {};
                if (frStep === 1) ve = validateStepPersonalAndAffiliation(data);
                else if (frStep === 2) ve = validateStep2Errors(data);
                else if (frStep === 3) ve = validatePhotoStepErrors(data);
                if (Object.keys(ve).length) {
                    Object.keys(ve).forEach(k => setFieldError(k, ve[k]));
                    showAlert('Please fix the highlighted fields.', 'error');
                    return;
                }
                if (frStep < frMaxStep) frStep += 1;
                syncFrWizardUi();
                refreshIcons();
            });
        }

        if (frBack) {
            frBack.addEventListener('click', function () {
                if (frStep <= 1) return;
                frStep -= 1;
                clearErrors(frForm);
                syncFrWizardUi();
            });
        }

        if (frForm) {
            frForm.addEventListener('submit', function (e) {
            e.preventDefault();
                if (frStep !== frMaxStep) return;
            clearErrors(this);
            const fd = new FormData(this);
            const data = Object.fromEntries(fd.entries());
                const ve = Object.assign({},
                    validateStepPersonalAndAffiliation(data),
                    validateStep2Errors(data),
                    validatePhotoStepErrors(data),
                    validateStepFinalErrors(data));
            if (Object.keys(ve).length) {
                Object.keys(ve).forEach(k => setFieldError(k, ve[k]));
                    const stepKeysPersonal = ['last_name','first_name','barangay'];
                    const stepKeysAffil = ['affiliation_role','ncfrs','rsbsa_registered','rsbsa_number'];
                    const hasStep1 = Object.keys(ve).some(k => stepKeysPersonal.indexOf(k) >= 0 || stepKeysAffil.indexOf(k) >= 0);
                    const stepKeysFarm = ['ownership_status','plant_area_value','plant_area_unit','liberica_bearing','liberica_non_bearing','robusta_bearing','robusta_non_bearing','excelsa_bearing','excelsa_non_bearing'];
                    const hasFarm = Object.keys(ve).some(k => stepKeysFarm.indexOf(k) >= 0 || k.indexOf('bearing') >= 0);
                    const hasPhoto = Object.keys(ve).some(k => k === 'profile_photo_data');
                    const stepKeysFinal = ['agree_registration'];
                    const hasFinal = Object.keys(ve).some(k => stepKeysFinal.indexOf(k) >= 0);
                    if (hasStep1) frStep = 1;
                    else if (hasFarm) frStep = 2;
                    else if (hasPhoto) frStep = 3;
                    else if (hasFinal) frStep = 4;
                    else frStep = 4;
                    syncFrWizardUi();
                showAlert('Please fix the highlighted fields.', 'error');
                return;
            }

                const submitBtn = document.getElementById('frWizardSubmit');
                const payload = collectWizardPayload(fd);
                const submitStartedAt = Date.now();

                var apiBaseFr = phpApiBase();
                var uidFr = 0;
                try {
                    var uRawFr = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
                    var uFr = uRawFr ? JSON.parse(uRawFr) : null;
                    uidFr = uFr && uFr.user_id ? parseInt(String(uFr.user_id), 10) : 0;
                } catch (_eUidFr) {}
                if (!apiBaseFr) {
                    showAlert('Registration saves to MySQL. Use http:// or https:// on the same machine where MySQL runs (e.g. this app URL), start MySQL, and import the Beanthentic schema.', 'error');
                    return;
                }
                if (!(uidFr > 0)) {
                    showAlert('Please sign in again (account id missing for registration).', 'error');
                    return;
                }
                var payloadWithUser = Object.assign({}, payload, { user_id: uidFr });

            const lockRegistrationFields = () => {
                    this.querySelectorAll('input, select, textarea').forEach(function (el) {
                        if (el.type === 'hidden') return;
                    if (el.tagName === 'SELECT' || el.type === 'checkbox' || el.type === 'radio') {
                        el.disabled = true;
                    } else if (typeof el.readOnly === 'boolean') {
                        el.readOnly = true;
                    }
                });
            };

            const wireFieldEditButtons = () => {
                this.querySelectorAll('button[data-edit-target]').forEach((btn) => {
                    if (btn.dataset.bound === '1') return;
                    btn.dataset.bound = '1';
                    btn.addEventListener('click', () => {
                        const targetId = btn.getAttribute('data-edit-target');
                        if (!targetId) return;
                        const field = document.getElementById(targetId);
                        if (!field) return;
                        field.disabled = false;
                        if (typeof field.readOnly === 'boolean') field.readOnly = false;
                        field.focus();
                        if (typeof field.select === 'function') field.select();
                    });
                });
            };
            wireFieldEditButtons();
            setRegisterButtonLoading(submitBtn, true);
                toggleSubmitOverlay('loading', true);

                const ctrl = (typeof AbortController !== 'undefined') ? new AbortController() : null;
                const timeoutId = setTimeout(function () {
                    if (ctrl) ctrl.abort();
                }, 20000);

            fetch(apiBaseFr + '/api/register_farm_farmer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payloadWithUser),
                    signal: ctrl ? ctrl.signal : undefined
            })
            .then(function (r) {
                return r.text().then(function (t) {
                    var j = null;
                    try {
                        j = JSON.parse(t);
                    } catch (_parseErr) {
                        var hint = (t && t.indexOf('<!') === 0) ? ' (server returned HTML instead of JSON — check XAMPP Apache and api/register_farm_farmer.php.)' : '';
                        throw new Error('Invalid response' + hint);
                    }
                    return { ok: r.ok, body: j };
                });
            })
            .then(({ ok, body }) => {
                    clearTimeout(timeoutId);
                if (body.success) {
                    farmerId = body.farmer_id;
                    sessionStorage.setItem(REGISTER_FARM_REG_OK, '1');
                    sessionStorage.setItem(REGISTER_FARM_FARMER_ID, String(farmerId));
                    try { localStorage.setItem('beanthentic_farmer_id', String(farmerId)); } catch (_e) {}
                    // Per-account registration state (so new accounts can register too).
                    try {
                        var uRaw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
                        var email = '';
                        if (uRaw) {
                            try {
                                var u = JSON.parse(uRaw);
                                if (u && u.email) email = String(u.email).trim().toLowerCase();
                            } catch (_pe) {}
                        }
                        if (email) {
                            var rawMap = localStorage.getItem('beanthentic_farmer_id_map') || sessionStorage.getItem('beanthentic_farmer_id_map');
                            var map = rawMap ? JSON.parse(rawMap) : {};
                            if (!map || typeof map !== 'object') map = {};
                            map[email] = String(farmerId);
                            localStorage.setItem('beanthentic_farmer_id_map', JSON.stringify(map));
                            sessionStorage.setItem('beanthentic_farmer_id_map', JSON.stringify(map));
                        }
                    } catch (_eMap) {}
                    try {
                        // Persist the exact registration inputs so Registration Summary can render them.
                        var farmerProfile = {
                            id: farmerId,
                            first_name: String(payload.first_name || '').trim(),
                            last_name: String(payload.last_name || '').trim(),
                            email: String(payload.email || '').trim(),
                            phone: String(payload.phone || '').trim(),
                            province: 'Batangas',
                            municipality: 'Lipa City',
                            barangay: String(payload.barangay || '').trim(),
                            federation: String(payload.federation || '').trim(),
                            association: String(payload.association || '').trim(),
                            ncfrs: String(payload.ncfrs || '').trim().toLowerCase(),
                            rsbsa_registered: String(payload.rsbsa_registered || '').trim(),
                            rsbsa_number: String(payload.rsbsa_number || '').trim(),
                            ownership_status: String(payload.ownership_status || '').trim(),
                            plant_area_value: payload.plant_area_value,
                            plant_area_unit: String(payload.plant_area_unit || '').trim(),
                            tree_counts: {
                                liberica: {
                                    bearing: payload.liberica_bearing,
                                    non_bearing: payload.liberica_non_bearing
                                },
                                robusta: {
                                    bearing: payload.robusta_bearing,
                                    non_bearing: payload.robusta_non_bearing
                                },
                                excelsa: {
                                    bearing: payload.excelsa_bearing,
                                    non_bearing: payload.excelsa_non_bearing
                                }
                            },
                            production_year: String(payload.production_year || '2026').trim(),
                            production: {
                                liberica: {
                                    qty: Number(payload.liberica_prod_qty || 0) || 0,
                                    unit: String(payload.liberica_prod_unit || 'kg').trim().toLowerCase()
                                },
                                robusta: {
                                    qty: Number(payload.robusta_prod_qty || 0) || 0,
                                    unit: String(payload.robusta_prod_unit || 'kg').trim().toLowerCase()
                                },
                                excelsa: {
                                    qty: Number(payload.excelsa_prod_qty || 0) || 0,
                                    unit: String(payload.excelsa_prod_unit || 'kg').trim().toLowerCase()
                                }
                            },
                            profile_photo_data: String(payload.profile_photo_data || '').trim(),
                        };
                        localStorage.setItem('beanthentic_farmer_profile', JSON.stringify(farmerProfile));
                        sessionStorage.setItem('beanthentic_farmer_profile', JSON.stringify(farmerProfile));
                        var profileEmail = String(payload.email || '').trim().toLowerCase();
                        var profileMapRaw = localStorage.getItem('beanthentic_farmer_profile_map') || sessionStorage.getItem('beanthentic_farmer_profile_map');
                        var profileMap = profileMapRaw ? JSON.parse(profileMapRaw) : {};
                        if (!profileMap || typeof profileMap !== 'object') profileMap = {};
                        var keySet = {};
                        function addKey(v) {
                            var k = String(v || '').trim().toLowerCase();
                            if (!k) return;
                            keySet[k] = true;
                            var d = k.replace(/\D/g, '');
                            if (!d) return;
                            if (d.indexOf('63') === 0 && d.length >= 12) keySet['0' + d.slice(2)] = true;
                            if (d.indexOf('0') === 0 && d.length >= 11) keySet['+63' + d.slice(1)] = true;
                            if (d.length === 10 && d.charAt(0) === '9') {
                                keySet['0' + d] = true;
                                keySet['+63' + d] = true;
                            }
                        }
                        addKey(profileEmail);
                        addKey(payload.phone);
                        try {
                            var uRaw2 = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
                            var u2 = uRaw2 ? JSON.parse(uRaw2) : null;
                            if (u2 && u2.email) addKey(u2.email);
                        } catch (_uMap) {}
                        Object.keys(keySet).forEach(function (k) {
                            profileMap[k] = farmerProfile;
                        });
                        localStorage.setItem('beanthentic_farmer_profile_map', JSON.stringify(profileMap));
                        sessionStorage.setItem('beanthentic_farmer_profile_map', JSON.stringify(profileMap));
                    } catch (_eProfile) {}
                    // Update account display name from the registration inputs (new users won't have a name yet).
                    try {
                        var emailKey = String(payload.email || '').trim().toLowerCase();
                        var fullName = (String(payload.last_name || '').trim() + ', ' + String(payload.first_name || '').trim())
                            .replace(/^,\s*/, '')
                            .trim();
                        if (emailKey && fullName) {
                            var mapRaw = localStorage.getItem('beanthentic_user_name_map') || sessionStorage.getItem('beanthentic_user_name_map');
                            var map = mapRaw ? JSON.parse(mapRaw) : {};
                            if (!map || typeof map !== 'object') map = {};
                            map[emailKey] = fullName;
                            localStorage.setItem('beanthentic_user_name_map', JSON.stringify(map));
                            sessionStorage.setItem('beanthentic_user_name_map', JSON.stringify(map));
                        }
                    } catch (_eNameMap) {}
                    try {
                        var uRaw = localStorage.getItem('beanthentic_user') || sessionStorage.getItem('beanthentic_user');
                        var u = uRaw ? JSON.parse(uRaw) : null;
                        if (u && u.email) {
                            u.needs_registration = false;
                            // Set name to the formatted full name if available.
                            var nm = (String(payload.last_name || '').trim() + ', ' + String(payload.first_name || '').trim())
                                .replace(/^,\s*/, '')
                                .trim();
                            if (nm) u.name = nm;
                            var up = JSON.stringify(u);
                            sessionStorage.setItem('beanthentic_user', up);
                            try { localStorage.setItem('beanthentic_user', up); } catch (_eStore) {}
                        }
                    } catch (_eUserUpdate) {}
                    try {
                        localStorage.removeItem('beanthentic_new_signup_login_id');
                        sessionStorage.removeItem('beanthentic_new_signup_login_id');
                    } catch (_e0) {}
                        try { syncRegisterNavIconFromStorage(); } catch (_e2) {}
                    lockRegistrationFields();
                    clearErrors(this);
                        const elapsed = Date.now() - submitStartedAt;
                        const waitMs = Math.max(0, 250 - elapsed);
                        setTimeout(function () {
                            toggleSubmitOverlay('loading', false);
                            toggleSubmitOverlay('success', true);
                            // Auto-open summary so user immediately sees submitted data.
                            setTimeout(function () {
                                goRegisterSummary();
                            }, 900);
                        }, waitMs);
                } else {
                        if (body.errors) {
                            applyServerErrors(body.errors);
                            moveWizardToStepForErrors(body.errors);
                        }
                    else showAlert(body.error || 'Registration failed.', 'error');
                        toggleSubmitOverlay('loading', false);
                }
                endRegisterButtonLoading(submitBtn);
            })
            .catch(err => {
                    clearTimeout(timeoutId);
                showAlert('Registration failed: ' + err.message, 'error');
                    toggleSubmitOverlay('loading', false);
                endRegisterButtonLoading(submitBtn);
            });
        });
        }

        function wireFieldClear(el) {
            const clear = () => {
                const name = el.getAttribute('name');
                if (!name) return;
                const span = document.querySelector('.field-error[data-error-for="' + name + '"]');
                if (span) span.textContent = '';
                el.classList.remove('input-invalid');
                const lab = el.closest('.checkbox-label');
                if (lab) lab.classList.remove('checkbox-invalid');
            };
            el.addEventListener('input', clear);
            el.addEventListener('change', clear);
        }
        document.querySelectorAll('#farmerForm input, #farmerForm select, #farmerForm textarea').forEach(wireFieldClear);

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-' + type;
            const iconName = type === 'success' ? 'check-circle' : 'alert-circle';
            alertDiv.innerHTML = '<i data-lucide="' + iconName + '" class="alert-icon"></i><span></span>';
            alertDiv.querySelector('span').textContent = message;
            const container = document.querySelector('.container');
            container.insertBefore(alertDiv, container.children[1] || container.firstChild);
            refreshIcons(alertDiv);
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            syncFrWizardUi();
            refreshIcons();
            syncRegisterNavIconFromStorage();

            const navBack = document.getElementById('frNavBack');
            if (navBack) {
                navBack.addEventListener('click', function () {
                    try {
                        if (window.history.length > 1) {
                            window.history.back();
                            return;
                        }
                    } catch (_e) {}
                    window.location.href = '/#home';
                });
            }
        });
    </script>

    <nav class="app-bottom-nav app-bottom-nav--mint" aria-label="Quick navigation">
        <div class="app-bottom-nav-inner">
            <a href="/#home" id="nav-home" class="app-bottom-nav-link">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </span>
                <span class="app-bottom-nav-label">Home</span>
            </a>
            <a href="/qr.php" id="nav-qr" class="app-bottom-nav-link">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon app-bottom-nav-icon--transaction" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6 7.25h9v2H6z"/><path fill="currentColor" d="M15 6 19 8.25 15 10.5z"/><path fill="currentColor" d="M9 14.25h9v2H9z"/><path fill="currentColor" d="M9 13.25 5 15.25 9 17.25z"/></svg>
                </span>
                <span class="app-bottom-nav-label">Transaction</span>
            </a>
            <a href="/register_summary.php" id="nav-register" class="app-bottom-nav-link app-bottom-nav-link--featured">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon app-bottom-nav-register-svg app-bottom-nav-register-svg--pending" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <svg class="app-bottom-nav-icon app-bottom-nav-register-svg app-bottom-nav-register-svg--complete" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </span>
                <span class="app-bottom-nav-label">Register</span>
            </a>
            <a href="/transaction-history.html" id="nav-history" class="app-bottom-nav-link app-bottom-nav-link--history">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                </span>
                <span class="app-bottom-nav-label">History</span>
            </a>
            <a href="/login.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin" data-no-loader="true">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon app-bottom-nav-icon--account" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.75"/><path d="M5.5 21v-.75a5 5 0 0 1 5-5h3a5 5 0 0 1 5 5v.75"/></svg>
                </span>
                <span class="app-bottom-nav-label">Account</span>
            </a>
        </div>
    </nav>
    <script>
        (function () {
            function syncMintBottomNavActive() {
                var bar = document.querySelector('.app-bottom-nav');
                if (!bar) return;
                var path = (location.pathname || '').toLowerCase();
                bar.querySelectorAll('.app-bottom-nav-link').forEach(function (a) {
                    a.removeAttribute('aria-current');
                });
                function setActive(sel) {
                    var el = bar.querySelector(sel);
                    if (!el) return;
                    el.setAttribute('aria-current', 'page');
                }
                if (path.indexOf('/register-farm') !== -1) {
                    setActive('#nav-register');
                } else if (path.indexOf('/maps') !== -1) {
                    setActive('#nav-home');
                }
            }

            function syncBottomNavAccount() {
                var a = document.getElementById('nav-signin');
                if (!a) return;
                var lbl = a.querySelector('.app-bottom-nav-label');
                function parseUser(raw) {
                    if (!raw) return null;
                    try {
                        var u = JSON.parse(raw);
                        if (u && u.email) return u;
                    } catch (_err) {}
                    return null;
                }
                var u = null;
                try {
                    u = parseUser(localStorage.getItem('beanthentic_user'));
                    if (u) {
                        try { sessionStorage.setItem('beanthentic_user', JSON.stringify(u)); } catch (_err2) {}
                    } else {
                        u = parseUser(sessionStorage.getItem('beanthentic_user'));
                        if (u) {
                            try { localStorage.setItem('beanthentic_user', JSON.stringify(u)); } catch (_err3) {}
                        }
                    }
                } catch (e) {}
                if (u && u.email) {
                    a.setAttribute('href', '/account.php');
                    if (lbl) lbl.textContent = 'Account';
                } else {
                    a.setAttribute('href', '/login.php');
                    if (lbl) lbl.textContent = 'Sign In';
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    syncBottomNavAccount();
                    syncMintBottomNavActive();
                    if (typeof syncRegisterNavIconFromStorage === 'function') syncRegisterNavIconFromStorage();
                });
            } else {
                syncBottomNavAccount();
                syncMintBottomNavActive();
                if (typeof syncRegisterNavIconFromStorage === 'function') syncRegisterNavIconFromStorage();
            }

            window.addEventListener('storage', function (e) {
                if (!e) return;
                if (e.key === 'beanthentic_user') syncBottomNavAccount();
                if ((e.key === 'beanthentic_farmer_id' || e.key === 'beanthentic_farmer_id_map') && typeof syncRegisterNavIconFromStorage === 'function') {
                    syncRegisterNavIconFromStorage();
                }
            });
            window.addEventListener('beanthentic-auth-changed', syncBottomNavAccount);

            document.addEventListener('DOMContentLoaded', function () {
                var a = document.getElementById('nav-signin');
                if (!a) return;
                a.addEventListener('click', function (e) {
                    function parseUser(raw) {
                        if (!raw) return null;
                        try {
                            var u = JSON.parse(raw);
                            if (u && u.email) return u;
                        } catch (_err) {}
                        return null;
                    }
                    var u = null;
                    try {
                        u = parseUser(localStorage.getItem('beanthentic_user')) || parseUser(sessionStorage.getItem('beanthentic_user'));
                    } catch (err) {}
                    e.preventDefault();
                    if (u && u.email) window.location.assign('/account.php');
                    else window.location.assign('/login.php');
                }, true);
            });
        })();
    </script>
</body>
</html>
        '''


