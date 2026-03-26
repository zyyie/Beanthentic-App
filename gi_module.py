from flask import Flask, request, jsonify, render_template_string
import sqlite3
import json
from datetime import datetime
import os

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
            try:
                data = request.get_json()
                conn = sqlite3.connect('gi_database.db')
                cursor = conn.cursor()
                
                cursor.execute('''
                    INSERT INTO farmers (name, email, phone, region, province, municipality, farm_address, farm_size)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ''', (
                    data['name'], data['email'], data.get('phone', ''),
                    data['region'], data['province'], data['municipality'],
                    data['farm_address'], data.get('farm_size', 0)
                ))
                
                farmer_id = cursor.lastrowid
                conn.commit()
                conn.close()
                
                return jsonify({
                    'success': True,
                    'farmer_id': farmer_id,
                    'message': 'Farmer registered successfully'
                })
            except Exception as e:
                return jsonify({'success': False, 'error': str(e)}), 400
        
        @self.app.route('/api/gi/applications', methods=['POST'])
        def submit_gi_application():
            try:
                data = request.get_json()
                conn = sqlite3.connect('gi_database.db')
                cursor = conn.cursor()
                
                cursor.execute('''
                    INSERT INTO gi_applications 
                    (farmer_id, coffee_variety_id, farm_location, elevation, soil_type, 
                     climate_info, cultivation_methods, processing_methods, unique_characteristics, 
                     historical_significance)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ''', (
                    data['farmer_id'], data['coffee_variety_id'], data['farm_location'],
                    data.get('elevation'), data.get('soil_type', ''), data.get('climate_info', ''),
                    data.get('cultivation_methods', ''), data.get('processing_methods', ''),
                    data.get('unique_characteristics', ''), data.get('historical_significance', '')
                ))
                
                application_id = cursor.lastrowid
                conn.commit()
                conn.close()
                
                return jsonify({
                    'success': True,
                    'application_id': application_id,
                    'message': 'GI application submitted successfully'
                })
            except Exception as e:
                return jsonify({'success': False, 'error': str(e)}), 400
        
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI Portal - Beanthentic Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #1a1a1a;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
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
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #4ade80;
        }
        
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
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            padding: 0.5rem;
            margin-bottom: 2rem;
            gap: 0.5rem;
        }
        
        .tab {
            flex: 1;
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
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
            justify-content: between;
            align-items: center;
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
            font-size: 1.25rem;
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
            .header h1 {
                font-size: 2rem;
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .form-grid {
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
                    <i class="fas fa-certificate"></i>
                </div>
                <h1>Geographical Indications Portal</h1>
                <p>Register your farm and apply for GI certification to protect and promote your unique coffee heritage</p>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="container">
            <a href="/" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </a>
            
            <div class="tabs">
                <button class="tab active" onclick="showTab('register')">
                    <i class="fas fa-user-plus"></i>
                    Register Farm
                </button>
                <button class="tab" onclick="showTab('apply')">
                    <i class="fas fa-file-alt"></i>
                    Apply for GI
                </button>
                <button class="tab" onclick="showTab('status')">
                    <i class="fas fa-search"></i>
                    Check Status
                </button>
            </div>
            
            <div id="register" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Farmer Registration</h2>
                            <p class="card-subtitle">Create your account to start the GI certification process</p>
                        </div>
                    </div>
                    
                    <form id="farmerForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="farmerName">Full Name <span class="required">*</span></label>
                                <input type="text" id="farmerName" name="name" required placeholder="Enter your full name">
                            </div>
                            
                            <div class="form-group">
                                <label for="farmerEmail">Email Address <span class="required">*</span></label>
                                <input type="email" id="farmerEmail" name="email" required placeholder="your.email@example.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="farmerPhone">Phone Number</label>
                                <input type="tel" id="farmerPhone" name="phone" placeholder="09XXXXXXXXX">
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
                            </div>
                            
                            <div class="form-group">
                                <label for="province">Province <span class="required">*</span></label>
                                <input type="text" id="province" name="province" required placeholder="Enter province name">
                            </div>
                            
                            <div class="form-group">
                                <label for="municipality">Municipality/City <span class="required">*</span></label>
                                <input type="text" id="municipality" name="municipality" required placeholder="Enter municipality or city">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="farmAddress">Complete Farm Address <span class="required">*</span></label>
                            <input type="text" id="farmAddress" name="farm_address" required placeholder="Enter complete farm address">
                        </div>
                        
                        <div class="form-group">
                            <label for="farmSize">Farm Size (hectares)</label>
                            <input type="number" id="farmSize" name="farm_size" step="0.01" min="0" placeholder="0.00">
                            <div class="form-help">Optional: Enter your total farm size in hectares</div>
                        </div>
                        
                        <button type="submit" class="btn">
                            <i class="fas fa-check"></i>
                            Register Farm
                        </button>
                    </form>
                </div>
            </div>
            
            <div id="apply" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h2 class="card-title">GI Certification Application</h2>
                            <p class="card-subtitle">Provide detailed information about your coffee's unique geographical characteristics</p>
                        </div>
                    </div>
                    
                    <form id="applicationForm">
                        <div class="form-group">
                            <label for="farmerId">Farmer ID <span class="required">*</span></label>
                            <input type="number" id="farmerId" name="farmer_id" required placeholder="Enter your Farmer ID">
                            <div class="form-help">Enter the Farmer ID you received after registration</div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="coffeeVariety">Coffee Variety <span class="required">*</span></label>
                                <select id="coffeeVariety" name="coffee_variety_id" required>
                                    <option value="">Select Coffee Variety</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="elevation">Elevation (meters above sea level)</label>
                                <input type="number" id="elevation" name="elevation" step="0.01" placeholder="1500">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="farmLocation">Farm Location Details <span class="required">*</span></label>
                            <input type="text" id="farmLocation" name="farm_location" required placeholder="Specific location details that make your farm unique">
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="soilType">Soil Type</label>
                                <input type="text" id="soilType" name="soil_type" placeholder="e.g., Volcanic loam, Clay soil">
                            </div>
                            
                            <div class="form-group">
                                <label for="climateInfo">Climate Information</label>
                                <textarea id="climateInfo" name="climate_info" placeholder="Describe the climate conditions in your farm area"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="cultivationMethods">Cultivation Methods</label>
                            <textarea id="cultivationMethods" name="cultivation_methods" placeholder="Describe your farming practices and cultivation methods"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="processingMethods">Processing Methods</label>
                            <textarea id="processingMethods" name="processing_methods" placeholder="Describe how you process your coffee beans"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="uniqueCharacteristics">Unique Geographical Characteristics</label>
                            <textarea id="uniqueCharacteristics" name="unique_characteristics" placeholder="What makes your coffee unique to your geographical location?"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="historicalSignificance">Historical Significance</label>
                            <textarea id="historicalSignificance" name="historical_significance" placeholder="Any historical or cultural significance of coffee farming in your area"></textarea>
                        </div>
                        
                        <button type="submit" class="btn">
                            <i class="fas fa-paper-plane"></i>
                            Submit Application
                        </button>
                    </form>
                </div>
            </div>
            
            <div id="status" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Application Status</h2>
                            <p class="card-subtitle">Track the progress of your GI certification applications</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="statusFarmerId">Enter Farmer ID:</label>
                        <div style="display: flex; gap: 1rem; align-items: end;">
                            <input type="number" id="statusFarmerId" placeholder="Enter your Farmer ID" style="flex: 1;">
                            <button type="button" class="btn" onclick="checkApplicationStatus()">
                                <i class="fas fa-search"></i>
                                Check Status
                            </button>
                        </div>
                    </div>
                    
                    <div id="statusResults"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let farmerId = null;
        
        // Load coffee varieties
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
        
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.closest('.tab').classList.add('active');
        }
        
        // Farmer registration form
        document.getElementById('farmerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            fetch('/api/gi/farmers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    farmerId = result.farmer_id;
                    showAlert('Registration successful! Your Farmer ID is: ' + farmerId, 'success');
                    this.reset();
                } else {
                    showAlert('Registration failed: ' + result.error, 'error');
                }
            })
            .catch(error => {
                showAlert('Registration failed: ' + error.message, 'error');
            });
        });
        
        // GI Application form
        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            fetch('/api/gi/applications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showAlert('Application submitted successfully! Application ID: ' + result.application_id, 'success');
                    this.reset();
                } else {
                    showAlert('Application failed: ' + result.error, 'error');
                }
            })
            .catch(error => {
                showAlert('Application failed: ' + error.message, 'error');
            });
        });
        
        function checkApplicationStatus() {
            const farmerId = document.getElementById('statusFarmerId').value;
            
            if (!farmerId) {
                showAlert('Please enter a Farmer ID', 'error');
                return;
            }
            
            fetch(`/api/gi/applications/${farmerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayApplications(data.applications);
                    } else {
                        showAlert('Failed to fetch applications: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    showAlert('Failed to fetch applications: ' + error.message, 'error');
                });
        }
        
        function displayApplications(applications) {
            const resultsDiv = document.getElementById('statusResults');
            
            if (applications.length === 0) {
                resultsDiv.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>No applications found for this Farmer ID.</p>
                    </div>
                `;
                return;
            }
            
            let html = '<h3 style="margin-bottom: 1.5rem; color: #1f2937;">Your Applications</h3>';
            
            applications.forEach(app => {
                const statusClass = app.status === 'approved' ? 'status-approved' : 
                                  app.status === 'rejected' ? 'status-rejected' : 'status-pending';
                
                const statusIcon = app.status === 'approved' ? 'fa-check-circle' : 
                                  app.status === 'rejected' ? 'fa-times-circle' : 'fa-clock';
                
                html += `
                    <div class="application-item">
                        <div class="application-header">
                            <div>
                                <div class="application-title">Application #${app.id}</div>
                                <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">
                                    <i class="fas fa-coffee"></i> ${app.variety_name}
                                </div>
                            </div>
                            <div class="status-badge ${statusClass}">
                                <i class="fas ${statusIcon}"></i>
                                ${app.status.toUpperCase()}
                            </div>
                        </div>
                        
                        <div class="application-details">
                            <div class="detail-item">
                                <span class="detail-label">Submitted Date</span>
                                <span class="detail-value">${new Date(app.submission_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Farm Location</span>
                                <span class="detail-value">${app.farm_location}</span>
                            </div>
                            ${app.elevation ? `
                            <div class="detail-item">
                                <span class="detail-label">Elevation</span>
                                <span class="detail-value">${app.elevation}m above sea level</span>
                            </div>
                            ` : ''}
                        </div>
                        
                        ${app.reviewer_notes ? `
                        <div style="margin-top: 1rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border-left: 4px solid #147539;">
                            <div style="font-weight: 500; color: #374151; margin-bottom: 0.25rem;">
                                <i class="fas fa-sticky-note"></i> Reviewer Notes
                            </div>
                            <div style="color: #6b7280; font-size: 0.875rem;">${app.reviewer_notes}</div>
                        </div>
                        ` : ''}
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = html;
        }
        
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            alertDiv.innerHTML = `
                <i class="fas ${icon} alert-icon"></i>
                <span>${message}</span>
            `;
            
            const container = document.querySelector('.container');
            container.insertBefore(alertDiv, container.firstChild.nextSibling);
            
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                setTimeout(() => {
                    alertDiv.remove();
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>
        '''
