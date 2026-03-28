from flask import request, jsonify, render_template_string
import sqlite3
import os
import re

EMAIL_RE = re.compile(r"^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$")


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


def validate_farmer_payload(data):
    errors = {}
    if not isinstance(data, dict):
        return None, {"_error": "Request body must be JSON."}
    name = (data.get("name") or "").strip()
    email = (data.get("email") or "").strip().lower()
    phone = _normalize_ph_phone(data.get("phone"))
    region = (data.get("region") or "").strip()
    province = (data.get("province") or "").strip()
    municipality = (data.get("municipality") or "").strip()
    farm_address = (data.get("farm_address") or "").strip()
    farm_size_raw = data.get("farm_size", 0)

    if len(name) < 3:
        errors["name"] = "Enter your full legal name (at least 3 characters)."
    elif len(name) > 120:
        errors["name"] = "Name must be at most 120 characters."
    if not EMAIL_RE.match(email):
        errors["email"] = "Enter a valid email address."
    if not phone:
        errors["phone"] = "Mobile number is required (09XXXXXXXXX) for GI verification and follow-up."
    elif not re.match(r"^09\d{9}$", phone):
        errors["phone"] = "Use Philippine mobile format: 09XXXXXXXXX (11 digits)."
    if region not in PH_REGIONS:
        errors["region"] = "Select a valid region from the list."
    if len(province) < 2 or len(province) > 80:
        errors["province"] = "Province must be 2–80 characters."
    if len(municipality) < 2 or len(municipality) > 80:
        errors["municipality"] = "City or municipality must be 2–80 characters."
    if len(farm_address) < 20 or len(farm_address) > 500:
        errors["farm_address"] = "Enter the complete farm address (at least 20 characters): sitio/purok, barangay, landmarks."

    if not _truthy(data.get("agree_registration")):
        errors["agree_registration"] = "Confirm that your registration details are true and complete."

    farm_size = 0.0
    if farm_size_raw not in (None, ""):
        try:
            farm_size = float(farm_size_raw)
        except (TypeError, ValueError):
            errors["farm_size"] = "Farm size must be a valid number."
        else:
            if farm_size < 0 or farm_size > 100_000:
                errors["farm_size"] = "Farm size must be between 0 and 100,000 hectares."

    if errors:
        return None, errors
    return {
        "name": name,
        "email": email,
        "phone": phone,
        "region": region,
        "province": province,
        "municipality": municipality,
        "farm_address": farm_address,
        "farm_size": farm_size,
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


class GIModule:
    def __init__(self, app):
        self.app = app
        self.init_database()
        self.setup_routes()
    
    def init_database(self):
        """Initialize SQLite database for GI data"""
        if not os.path.exists('gi_database.db'):
            conn = sqlite3.connect('gi_database.db')
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
                    farm_address TEXT NOT NULL,
                    farm_size REAL,
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
    
    def setup_routes(self):
        """Setup API routes for GI module"""
        
        @self.app.route('/api/gi/farmers', methods=['POST'])
        def register_farmer():
            data = request.get_json(silent=True)
            cleaned, errs = validate_farmer_payload(data)
            if errs:
                return jsonify({'success': False, 'errors': errs}), 400
            try:
                conn = sqlite3.connect('gi_database.db')
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT INTO farmers (name, email, phone, region, province, municipality, farm_address, farm_size)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ''', (
                    cleaned['name'], cleaned['email'], cleaned['phone'],
                    cleaned['region'], cleaned['province'], cleaned['municipality'],
                    cleaned['farm_address'], cleaned['farm_size'],
                ))
                farmer_id = cursor.lastrowid
                conn.commit()
                conn.close()
                return jsonify({
                    'success': True,
                    'farmer_id': farmer_id,
                    'message': 'Farmer registered successfully',
                })
            except sqlite3.IntegrityError:
                return jsonify({
                    'success': False,
                    'errors': {'email': 'This email is already registered.'},
                }), 400
            except Exception as e:
                return jsonify({'success': False, 'errors': {'_error': str(e)}}), 400
        
        @self.app.route('/api/gi/applications', methods=['POST'])
        def submit_gi_application():
            data = request.get_json(silent=True)
            conn = sqlite3.connect('gi_database.db')
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
                    'message': 'GI application submitted successfully',
                })
            except Exception as e:
                conn.close()
                return jsonify({'success': False, 'errors': {'_error': str(e)}}), 400
        
        @self.app.route('/api/gi/varieties', methods=['GET'])
        def get_coffee_varieties():
            try:
                conn = sqlite3.connect('gi_database.db')
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
        
        @self.app.route('/api/gi/applications/<int:farmer_id>', methods=['GET'])
        def get_farmer_applications(farmer_id):
            try:
                conn = sqlite3.connect('gi_database.db')
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
        
        @self.app.route('/gi')
        def gi_portal():
            """Serve GI portal page"""
            return render_template_string(self.get_gi_portal_html())
    
    def get_gi_portal_html(self):
        """Return HTML for GI portal"""
        return '''
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#147539">
    <meta name="color-scheme" content="light">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>GI Portal - Beanthentic Coffee</title>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            line-height: 1.6;
            min-height: 100vh;
            min-height: 100dvh;
            padding: max(0px, env(safe-area-inset-top)) max(0px, env(safe-area-inset-right)) max(0px, env(safe-area-inset-bottom)) max(0px, env(safe-area-inset-left));
            -webkit-tap-highlight-color: rgba(20, 117, 57, 0.12);
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
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .process-step {
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(20, 117, 57, 0.15);
            border-radius: 14px;
            padding: 1rem 0.85rem;
            text-align: center;
            box-shadow: 0 4px 14px rgba(17, 24, 39, 0.06);
        }
        .process-step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: linear-gradient(135deg, #147539 0%, #0f5a2c 100%);
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
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
            color: #147539;
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
            border: 1px solid rgba(20, 117, 57, 0.2);
            border-radius: 12px;
            padding: 1rem 1.15rem;
            font-size: 0.875rem;
            color: #065f46;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .callout-info strong { color: #047857; }
        
        .header {
            background: linear-gradient(135deg, #147539 0%, #0f5a2c 100%);
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
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .header-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
            color: #86efac;
        }
        .header-icon svg, .card-icon svg, .back-link svg, .btn svg, .tab svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
        .header-icon svg { width: 3rem; height: 3rem; }
        .card-icon svg { width: 1.5rem; height: 1.5rem; }
        .field-error {
            display: block;
            font-size: 0.8125rem;
            color: #b91c1c;
            margin-top: 0.35rem;
            min-height: 1.1em;
        }
        input.input-invalid, select.input-invalid, textarea.input-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
        }
        .status-badge svg { width: 1rem; height: 1rem; vertical-align: middle; margin-right: 0.25rem; }
        
        .main-content {
            padding: 3rem 0;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #147539;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: #0f5a2c;
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
            background: linear-gradient(135deg, #147539 0%, #0f5a2c 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(20, 117, 57, 0.3);
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
            background: linear-gradient(135deg, #147539 0%, #0f5a2c 100%);
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
            border-color: #147539;
            box-shadow: 0 0 0 3px rgba(20, 117, 57, 0.1);
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
            background: linear-gradient(135deg, #147539 0%, #0f5a2c 100%);
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
            box-shadow: 0 4px 6px rgba(20, 117, 57, 0.2);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(20, 117, 57, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
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
            border-color: #147539;
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
            color: #147539;
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
            border-left: 4px solid #147539;
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
            accent-color: #147539;
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
            background: linear-gradient(90deg, #147539 0%, #4ade80 100%);
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
            color: #147539;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 2rem 0;
            }
            .header h1 {
                font-size: clamp(1.35rem, 5vw, 2rem);
                padding: 0 0.5rem;
            }
            .header p { font-size: 1rem; padding: 0 0.5rem; }
            .main-content { padding: 1.75rem 0; }
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
                grid-template-columns: 1fr;
                gap: 0.65rem;
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-icon">
                    <i data-lucide="badge-check"></i>
                </div>
                <h1>Geographical Indications Portal</h1>
                <p>Register your farm, document origin-linked qualities, and track GI certification—built for Philippine coffee growers.</p>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="container">
            <a href="/" class="back-link">
                <i data-lucide="arrow-left"></i>
                Back to Home
            </a>

            <div class="app-shell-intro">
                <p class="app-tagline">
                    A <strong>Geographical Indication (GI)</strong> helps buyers recognize coffee tied to your region’s climate, soil, and farming traditions.
                    Use this portal to submit structured farm data so reviewers can evaluate uniqueness, consistency, and traceability—key criteria in GI programs worldwide.
                </p>
                <div class="process-rail" aria-label="Three steps">
                    <div class="process-step">
                        <div class="process-step-num">1</div>
                        <h3>Register farm</h3>
                        <p>Official contact, location, and farm footprint in one profile.</p>
                    </div>
                    <div class="process-step">
                        <div class="process-step-num">2</div>
                        <h3>Apply for GI</h3>
                        <p>Describe terroir, practices, and what makes your cup distinctive.</p>
                    </div>
                    <div class="process-step">
                        <div class="process-step-num">3</div>
                        <h3>Track status</h3>
                        <p>Follow pending, approved, or returned applications with your Farmer ID.</p>
                    </div>
                </div>
                <div class="detail-grid">
                    <div class="detail-tile">
                        <h4><i data-lucide="clipboard-list"></i> What to prepare</h4>
                        <ul>
                            <li>Valid email and PH mobile (09XXXXXXXXX)</li>
                            <li>Region, province, city/municipality, full farm address</li>
                            <li>Optional farm size (hectares) for context</li>
                        </ul>
                    </div>
                    <div class="detail-tile">
                        <h4><i data-lucide="shield-check"></i> Data &amp; privacy</h4>
                        <ul>
                            <li>Information is stored for GI workflow review</li>
                            <li>Use accurate details—errors may delay certification</li>
                            <li>Keep your Farmer ID private; needed to apply and check status</li>
                        </ul>
                    </div>
                    <div class="detail-tile">
                        <h4><i data-lucide="clock"></i> Review timeline</h4>
                        <ul>
                            <li>Applications start as <strong>pending</strong></li>
                            <li>Reviewers may add notes when status changes</li>
                            <li>Check the Status tab anytime with your Farmer ID</li>
                        </ul>
                    </div>
                </div>
                <div class="callout-info">
                    <strong>Tip:</strong> Complete every required field. For “Farm location details,” describe landmarks, elevation context, and barangay-level specificity—stronger applications reference soil, microclimate, and post-harvest steps.
                </div>
            </div>
            
            <div class="tabs" role="tablist">
                <button type="button" class="tab active" onclick="showTab('register', this)" role="tab" aria-selected="true">
                    <i data-lucide="user-plus"></i>
                    Register Farm
                </button>
                <button type="button" class="tab" onclick="showTab('apply', this)" role="tab" aria-selected="false">
                    <i data-lucide="file-text"></i>
                    Apply for GI
                </button>
                <button type="button" class="tab" onclick="showTab('status', this)" role="tab" aria-selected="false">
                    <i data-lucide="search"></i>
                    Check Status
                </button>
            </div>
            
            <div id="register" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i data-lucide="user-plus"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Farmer Registration</h2>
                            <p class="card-subtitle">Create your account to start the GI certification process</p>
                        </div>
                    </div>
                    
                    <form id="farmerForm" novalidate>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="farmerName">Full Name <span class="required">*</span></label>
                                <input type="text" id="farmerName" name="name" required minlength="3" maxlength="120" autocomplete="name" placeholder="Your full legal name (at least 3 letters)">
                                <span class="field-error" data-error-for="name" role="alert"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="farmerEmail">Email Address <span class="required">*</span></label>
                                <input type="email" id="farmerEmail" name="email" required maxlength="254" autocomplete="email" placeholder="your.email@example.com">
                                <span class="field-error" data-error-for="email" role="alert"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="farmerPhone">Mobile number <span class="required">*</span></label>
                                <input type="tel" id="farmerPhone" name="phone" required maxlength="13" inputmode="numeric" placeholder="09XXXXXXXXX" pattern="[0-9+]*" autocomplete="tel">
                                <div class="form-help">Required: Philippine mobile only (11 digits starting with 09).</div>
                                <span class="field-error" data-error-for="phone" role="alert"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="region">Region <span class="required">*</span></label>
                                <select id="region" name="region" required>
                                    <option value="">Select Region</option>
                                    <option value="Ilocos Region">Ilocos Region (Region I)</option>
                                    <option value="Cagayan Valley">Cagayan Valley (Region II)</option>
                                    <option value="Central Luzon">Central Luzon (Region III)</option>
                                    <option value="CALABARZON">CALABARZON (Region IV-A)</option>
                                    <option value="MIMAROPA">MIMAROPA (Region IV-B)</option>
                                    <option value="Bicol Region">Bicol Region (Region V)</option>
                                    <option value="Western Visayas">Western Visayas (Region VI)</option>
                                    <option value="Central Visayas">Central Visayas (Region VII)</option>
                                    <option value="Eastern Visayas">Eastern Visayas (Region VIII)</option>
                                    <option value="Zamboanga Peninsula">Zamboanga Peninsula (Region IX)</option>
                                    <option value="Northern Mindanao">Northern Mindanao (Region X)</option>
                                    <option value="Davao Region">Davao Region (Region XI)</option>
                                    <option value="SOCCSKSARGEN">SOCCSKSARGEN (Region XII)</option>
                                    <option value="CARAGA">CARAGA (Region XIII)</option>
                                    <option value="CAR">Cordillera Administrative Region (CAR)</option>
                                    <option value="NCR">National Capital Region (NCR)</option>
                                </select>
                                <span class="field-error" data-error-for="region" role="alert"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="province">Province <span class="required">*</span></label>
                                <input type="text" id="province" name="province" required maxlength="80" placeholder="Enter province name">
                                <span class="field-error" data-error-for="province" role="alert"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="municipality">Municipality/City <span class="required">*</span></label>
                                <input type="text" id="municipality" name="municipality" required maxlength="80" placeholder="Enter municipality or city">
                                <span class="field-error" data-error-for="municipality" role="alert"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="farmAddress">Complete Farm Address <span class="required">*</span></label>
                            <input type="text" id="farmAddress" name="farm_address" required minlength="20" maxlength="500" placeholder="Sitio/purok, barangay, landmarks (at least 20 characters)">
                            <span class="field-error" data-error-for="farm_address" role="alert"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="farmSize">Farm Size (hectares)</label>
                            <input type="number" id="farmSize" name="farm_size" step="0.01" min="0" max="100000" placeholder="0.00">
                            <div class="form-help">Optional: Enter your total farm size in hectares</div>
                            <span class="field-error" data-error-for="farm_size" role="alert"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label" for="agreeRegistration">
                                <input type="checkbox" id="agreeRegistration" name="agree_registration" value="yes">
                                <span>I certify that my registration details are <strong>true and complete</strong> to the best of my knowledge, and I understand false information may affect GI eligibility.</span>
                            </label>
                            <span class="field-error" data-error-for="agree_registration" role="alert"></span>
                        </div>
                        
                        <button type="submit" class="btn">
                            <i data-lucide="check"></i>
                            Register Farm
                        </button>
                    </form>
                </div>
            </div>
            
            <div id="apply" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i data-lucide="file-text"></i>
                        </div>
                        <div>
                            <h2 class="card-title">GI Certification Application</h2>
                            <p class="card-subtitle">Provide detailed information about your coffee's unique geographical characteristics</p>
                        </div>
                    </div>
                    
                    <form id="applicationForm" novalidate>
                        <div class="form-group">
                            <label for="farmerId">Farmer ID <span class="required">*</span></label>
                            <input type="number" id="farmerId" name="farmer_id" required min="1" step="1" placeholder="Enter your Farmer ID">
                            <div class="form-help">Enter the Farmer ID you received after registration</div>
                            <span class="field-error" data-error-for="farmer_id" role="alert"></span>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="coffeeVariety">Coffee Variety <span class="required">*</span></label>
                                <select id="coffeeVariety" name="coffee_variety_id" required>
                                    <option value="">Select Coffee Variety</option>
                                </select>
                                <span class="field-error" data-error-for="coffee_variety_id" role="alert"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="elevation">Elevation (meters above sea level)</label>
                                <input type="number" id="elevation" name="elevation" step="0.01" min="0" max="9000" placeholder="1500">
                                <span class="field-error" data-error-for="elevation" role="alert"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="farmLocation">Farm Location Details <span class="required">*</span></label>
                            <input type="text" id="farmLocation" name="farm_location" required minlength="40" maxlength="2000" placeholder="At least 40 characters: routes, terrain, barangay context, what makes the site distinct">
                            <span class="field-error" data-error-for="farm_location" role="alert"></span>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="soilType">Soil Type <span class="required">*</span></label>
                                <input type="text" id="soilType" name="soil_type" required maxlength="200" placeholder="e.g. volcanic loam (required)">
                                <span class="field-error" data-error-for="soil_type" role="alert"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="climateInfo">Climate Information <span class="required">*</span></label>
                                <textarea id="climateInfo" name="climate_info" required maxlength="2000" minlength="20" placeholder="At least 20 characters: rainfall, temperature range, mist, dry season…"></textarea>
                                <span class="field-error" data-error-for="climate_info" role="alert"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="cultivationMethods">Cultivation Methods <span class="required">*</span></label>
                            <textarea id="cultivationMethods" name="cultivation_methods" required maxlength="2000" minlength="20" placeholder="At least 20 characters: shade, pruning, fertilizer, organic practices…"></textarea>
                            <span class="field-error" data-error-for="cultivation_methods" role="alert"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="processingMethods">Processing Methods <span class="required">*</span></label>
                            <textarea id="processingMethods" name="processing_methods" required maxlength="2000" minlength="20" placeholder="At least 20 characters: washed, natural, drying beds, hulling…"></textarea>
                            <span class="field-error" data-error-for="processing_methods" role="alert"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="uniqueCharacteristics">Unique Geographical Characteristics <span class="required">*</span></label>
                            <textarea id="uniqueCharacteristics" name="unique_characteristics" required maxlength="2000" minlength="30" placeholder="At least 30 characters: terroir, altitude effect, cup qualities tied to this place"></textarea>
                            <span class="field-error" data-error-for="unique_characteristics" role="alert"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="historicalSignificance">Historical Significance</label>
                            <textarea id="historicalSignificance" name="historical_significance" maxlength="2000" placeholder="Any historical or cultural significance of coffee farming in your area"></textarea>
                            <span class="field-error" data-error-for="historical_significance" role="alert"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label" for="agreeDeclaration">
                                <input type="checkbox" id="agreeDeclaration" name="agree_declaration" value="yes">
                                <span>I declare that this GI application is <strong>accurate</strong>; I understand reviewers may verify details and that misleading information can lead to rejection.</span>
                            </label>
                            <span class="field-error" data-error-for="agree_declaration" role="alert"></span>
                        </div>
                        
                        <button type="submit" class="btn">
                            <i data-lucide="send"></i>
                            Submit Application
                        </button>
                    </form>
                </div>
            </div>
            
            <div id="status" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i data-lucide="search"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Application Status</h2>
                            <p class="card-subtitle">Track the progress of your GI certification applications</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="statusFarmerId">Enter Farmer ID:</label>
                        <div class="status-row">
                            <input type="number" id="statusFarmerId" min="1" step="1" placeholder="Enter your Farmer ID" inputmode="numeric">
                            <button type="button" class="btn" onclick="checkApplicationStatus()">
                                <i data-lucide="search"></i>
                                Check Status
                            </button>
                        </div>
                        <span class="field-error" data-error-for="status_farmer_id" role="alert"></span>
                    </div>
                    
                    <div id="statusResults"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    <script>
        let farmerId = null;
        const EMAIL_RE = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$/;
        const PH_REGIONS = new Set(["Ilocos Region","Cagayan Valley","Central Luzon","CALABARZON","MIMAROPA","Bicol Region","Western Visayas","Central Visayas","Eastern Visayas","Zamboanga Peninsula","Northern Mindanao","Davao Region","SOCCSKSARGEN","CARAGA","CAR","NCR"]);

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

        function normalizePhone(v) {
            if (!v) return '';
            let s = String(v).replace(/[\\s\\-]/g, '');
            if (s.startsWith('+63')) s = '0' + s.slice(3);
            else if (s.startsWith('63') && s.length >= 11) s = '0' + s.slice(2);
            return s;
        }

        function validateFarmerClient(d) {
            const err = {};
            const name = (d.name || '').trim();
            const email = (d.email || '').trim().toLowerCase();
            const phone = normalizePhone(d.phone);
            const region = (d.region || '').trim();
            const province = (d.province || '').trim();
            const municipality = (d.municipality || '').trim();
            const farm_address = (d.farm_address || '').trim();
            let farm_size = d.farm_size;
            if (name.length < 3) err.name = 'Enter your full legal name (at least 3 characters).';
            else if (name.length > 120) err.name = 'Name must be at most 120 characters.';
            if (!EMAIL_RE.test(email)) err.email = 'Enter a valid email address.';
            if (!phone) err.phone = 'Mobile number is required (09XXXXXXXXX).';
            else if (!/^09\\d{9}$/.test(phone)) err.phone = 'Use Philippine mobile format: 09XXXXXXXXX.';
            if (!PH_REGIONS.has(region)) err.region = 'Select a valid region.';
            if (province.length < 2 || province.length > 80) err.province = 'Province must be 2–80 characters.';
            if (municipality.length < 2 || municipality.length > 80) err.municipality = 'City or municipality must be 2–80 characters.';
            if (farm_address.length < 20 || farm_address.length > 500) err.farm_address = 'Farm address must be at least 20 characters (complete sitio/purok and landmarks).';
            if (farm_size !== '' && farm_size != null) {
                const fs = parseFloat(farm_size);
                if (isNaN(fs)) err.farm_size = 'Farm size must be a valid number.';
                else if (fs < 0 || fs > 100000) err.farm_size = 'Farm size must be between 0 and 100,000 hectares.';
            }
            if (d.agree_registration !== 'yes') err.agree_registration = 'Please confirm the declaration above.';
            return err;
        }

        function validateApplicationClient(d) {
            const err = {};
            const fid = parseInt(d.farmer_id, 10);
            const vid = parseInt(d.coffee_variety_id, 10);
            const farm_location = (d.farm_location || '').trim();
            const soil = (d.soil_type || '').trim();
            const climate = (d.climate_info || '').trim();
            const cult = (d.cultivation_methods || '').trim();
            const proc = (d.processing_methods || '').trim();
            const uniq = (d.unique_characteristics || '').trim();
            if (isNaN(fid) || fid < 1) err.farmer_id = 'Enter a valid Farmer ID.';
            if (isNaN(vid) || vid < 1) err.coffee_variety_id = 'Select a coffee variety.';
            if (farm_location.length < 40) err.farm_location = 'Farm location details must be at least 40 characters.';
            else if (farm_location.length > 2000) err.farm_location = 'Farm location must be at most 2000 characters.';
            if (soil.length < 3) err.soil_type = 'Describe soil type (at least 3 characters).';
            if (climate.length < 20) err.climate_info = 'Climate section must be at least 20 characters.';
            if (cult.length < 20) err.cultivation_methods = 'Cultivation section must be at least 20 characters.';
            if (proc.length < 20) err.processing_methods = 'Processing section must be at least 20 characters.';
            if (uniq.length < 30) err.unique_characteristics = 'Geographical uniqueness must be at least 30 characters.';
            const el = d.elevation;
            if (el !== '' && el != null) {
                const e = parseFloat(el);
                if (isNaN(e)) err.elevation = 'Elevation must be a valid number.';
                else if (e < 0 || e > 9000) err.elevation = 'Elevation must be between 0 and 9,000 meters.';
            }
            if (d.agree_declaration !== 'yes') err.agree_declaration = 'Please accept the declaration to submit.';
            return err;
        }

        fetch('/api/gi/varieties')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('coffeeVariety');
                    data.varieties.forEach(variety => {
                        const option = document.createElement('option');
                        option.value = variety.id;
                        option.textContent = variety.name + ' - ' + variety.description;
                        select.appendChild(option);
                    });
                }
            });

        function showTab(tabName, btn) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
            const panel = document.getElementById(tabName);
            if (panel) panel.classList.add('active');
            if (btn) {
                btn.classList.add('active');
                btn.setAttribute('aria-selected', 'true');
            }
            refreshIcons();
        }

        document.getElementById('farmerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors(this);
            const fd = new FormData(this);
            const data = Object.fromEntries(fd.entries());
            const ve = validateFarmerClient(data);
            if (Object.keys(ve).length) {
                Object.keys(ve).forEach(k => setFieldError(k, ve[k]));
                showAlert('Please fix the highlighted fields.', 'error');
                return;
            }
            const payload = {
                name: data.name.trim(),
                email: data.email.trim().toLowerCase(),
                phone: normalizePhone(data.phone),
                region: data.region,
                province: data.province.trim(),
                municipality: data.municipality.trim(),
                farm_address: data.farm_address.trim(),
                farm_size: data.farm_size === '' ? 0 : parseFloat(data.farm_size),
                agree_registration: data.agree_registration === 'yes' ? 'yes' : ''
            };
            fetch('/api/gi/farmers', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(r => r.json().then(j => ({ ok: r.ok, body: j })))
            .then(({ ok, body }) => {
                if (body.success) {
                    farmerId = body.farmer_id;
                    showAlert('Registration successful! Your Farmer ID is: ' + farmerId, 'success');
                    this.reset();
                    clearErrors(this);
                } else {
                    if (body.errors) applyServerErrors(body.errors);
                    else showAlert(body.error || 'Registration failed.', 'error');
                }
            })
            .catch(err => showAlert('Registration failed: ' + err.message, 'error'));
        });

        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors(this);
            const fd = new FormData(this);
            const data = Object.fromEntries(fd.entries());
            const ve = validateApplicationClient(data);
            if (Object.keys(ve).length) {
                Object.keys(ve).forEach(k => setFieldError(k, ve[k]));
                showAlert('Please fix the highlighted fields.', 'error');
                return;
            }
            const payload = {
                farmer_id: parseInt(data.farmer_id, 10),
                coffee_variety_id: parseInt(data.coffee_variety_id, 10),
                farm_location: data.farm_location.trim(),
                elevation: data.elevation === '' ? null : parseFloat(data.elevation),
                soil_type: (data.soil_type || '').trim(),
                climate_info: (data.climate_info || '').trim(),
                cultivation_methods: (data.cultivation_methods || '').trim(),
                processing_methods: (data.processing_methods || '').trim(),
                unique_characteristics: (data.unique_characteristics || '').trim(),
                historical_significance: (data.historical_significance || '').trim(),
                agree_declaration: data.agree_declaration === 'yes' ? 'yes' : ''
            };
            fetch('/api/gi/applications', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(r => r.json().then(j => ({ ok: r.ok, body: j })))
            .then(({ ok, body }) => {
                if (body.success) {
                    showAlert('Application submitted successfully! Application ID: ' + body.application_id, 'success');
                    this.reset();
                    clearErrors(this);
                } else {
                    if (body.errors) applyServerErrors(body.errors);
                    else showAlert(body.error || 'Application failed.', 'error');
                }
            })
            .catch(err => showAlert('Application failed: ' + err.message, 'error'));
        });

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
        document.querySelectorAll('#farmerForm input, #farmerForm select, #applicationForm input, #applicationForm select, #applicationForm textarea').forEach(wireFieldClear);
        document.getElementById('statusFarmerId').addEventListener('input', function() {
            const span = document.querySelector('.field-error[data-error-for="status_farmer_id"]');
            if (span) span.textContent = '';
        });

        function checkApplicationStatus() {
            clearErrors(document.getElementById('statusResults'));
            const sid = document.getElementById('statusFarmerId');
            const errSpan = document.querySelector('.field-error[data-error-for="status_farmer_id"]');
            const raw = sid.value.trim();
            if (!raw) {
                if (errSpan) errSpan.textContent = 'Enter your Farmer ID.';
                showAlert('Please enter a Farmer ID.', 'error');
                return;
            }
            const n = parseInt(raw, 10);
            if (isNaN(n) || n < 1) {
                if (errSpan) errSpan.textContent = 'Farmer ID must be a positive number.';
                return;
            }
            if (errSpan) errSpan.textContent = '';
            fetch('/api/gi/applications/' + n)
                .then(response => response.json())
                .then(data => {
                    if (data.success) displayApplications(data.applications);
                    else showAlert(data.error || 'Could not load applications.', 'error');
                })
                .catch(error => showAlert('Failed to fetch applications: ' + error.message, 'error'));
        }

        function escapeHtml(s) {
            if (!s) return '';
            const d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }

        function displayApplications(applications) {
            const resultsDiv = document.getElementById('statusResults');
            if (applications.length === 0) {
                resultsDiv.innerHTML = '<div class="empty-apps"><i data-lucide="inbox"></i><p>No applications found for this Farmer ID.</p></div>';
                refreshIcons(resultsDiv);
                return;
            }
            let html = '<h3 class="apps-heading">Your Applications</h3>';
            applications.forEach(app => {
                const statusClass = app.status === 'approved' ? 'status-approved' : app.status === 'rejected' ? 'status-rejected' : 'status-pending';
                const statusLucide = app.status === 'approved' ? 'check-circle' : app.status === 'rejected' ? 'x-circle' : 'clock';
                html += '<div class="application-item">' +
                    '<div class="application-header">' +
                    '<div><div class="application-title">Application #' + app.id + '</div>' +
                    '<div class="variety-line"><i data-lucide="coffee"></i> ' + escapeHtml(app.variety_name) + '</div></div>' +
                    '<div class="status-badge ' + statusClass + '"><i data-lucide="' + statusLucide + '"></i> ' + app.status.toUpperCase() + '</div></div>' +
                    '<div class="application-details">' +
                    '<div class="detail-item"><span class="detail-label">Submitted Date</span>' +
                    '<span class="detail-value">' + escapeHtml(String(new Date(app.submission_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }))) + '</span></div>' +
                    '<div class="detail-item"><span class="detail-label">Farm Location</span>' +
                    '<span class="detail-value">' + escapeHtml(app.farm_location) + '</span></div>';
                if (app.elevation) {
                    html += '<div class="detail-item"><span class="detail-label">Elevation</span>' +
                        '<span class="detail-value">' + escapeHtml(String(app.elevation)) + 'm above sea level</span></div>';
                }
                html += '</div>';
                if (app.reviewer_notes) {
                    html += '<div class="reviewer-box"><div class="reviewer-title"><i data-lucide="sticky-note"></i> Reviewer Notes</div>' +
                        '<div class="reviewer-text">' + escapeHtml(app.reviewer_notes) + '</div></div>';
                }
                html += '</div>';
            });
            resultsDiv.innerHTML = html;
            refreshIcons(resultsDiv);
        }

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

        document.addEventListener('DOMContentLoaded', function() { refreshIcons(); });
    </script>
</body>
</html>
        '''
