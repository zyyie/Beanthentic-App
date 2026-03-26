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
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        
        body {
            color: #25130c;
            background: #fff9f2;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        
        .header {
            background: #147539;
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .header h1 {
            text-align: center;
            font-size: 2rem;
        }
        
        .tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
            margin-bottom: 2rem;
        }
        
        .tab {
            padding: 1rem 2rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            color: #666;
            border-bottom: 3px solid transparent;
        }
        
        .tab.active {
            color: #147539;
            border-bottom-color: #147539;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .btn {
            background: linear-gradient(135deg, #8b4a2b, #3ea642);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .application-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: #147539;
            text-decoration: none;
            font-weight: 600;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Geographical Indications Portal</h1>
            <p style="text-align: center; margin-top: 0.5rem;">Register your farm and apply for GI certification</p>
        </div>
    </div>
    
    <div class="container">
        <a href="/" class="back-link">← Back to Home</a>
        
        <div class="tabs">
            <button class="tab active" onclick="showTab('register')">Register Farm</button>
            <button class="tab" onclick="showTab('apply')">Apply for GI</button>
            <button class="tab" onclick="showTab('status')">Check Status</button>
        </div>
        
        <div id="register" class="tab-content active">
            <div class="card">
                <h2>Farmer Registration</h2>
                <form id="farmerForm">
                    <div class="form-group">
                        <label for="farmerName">Full Name *</label>
                        <input type="text" id="farmerName" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="farmerEmail">Email *</label>
                        <input type="email" id="farmerEmail" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="farmerPhone">Phone Number</label>
                        <input type="tel" id="farmerPhone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="region">Region *</label>
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
                        <label for="province">Province *</label>
                        <input type="text" id="province" name="province" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="municipality">Municipality/City *</label>
                        <input type="text" id="municipality" name="municipality" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="farmAddress">Farm Address *</label>
                        <input type="text" id="farmAddress" name="farm_address" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="farmSize">Farm Size (hectares)</label>
                        <input type="number" id="farmSize" name="farm_size" step="0.01" min="0">
                    </div>
                    
                    <button type="submit" class="btn">Register Farm</button>
                </form>
            </div>
        </div>
        
        <div id="apply" class="tab-content">
            <div class="card">
                <h2>GI Certification Application</h2>
                <form id="applicationForm">
                    <div class="form-group">
                        <label for="farmerId">Farmer ID *</label>
                        <input type="number" id="farmerId" name="farmer_id" required>
                        <small>Enter the Farmer ID you received after registration</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="coffeeVariety">Coffee Variety *</label>
                        <select id="coffeeVariety" name="coffee_variety_id" required>
                            <option value="">Select Coffee Variety</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="farmLocation">Farm Location Details *</label>
                        <input type="text" id="farmLocation" name="farm_location" required>
                        <small>Specific location details that make your farm unique</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="elevation">Elevation (meters above sea level)</label>
                        <input type="number" id="elevation" name="elevation" step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="soilType">Soil Type</label>
                        <input type="text" id="soilType" name="soil_type">
                    </div>
                    
                    <div class="form-group">
                        <label for="climateInfo">Climate Information</label>
                        <textarea id="climateInfo" name="climate_info" placeholder="Describe the climate conditions in your farm area"></textarea>
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
                        <label for="uniqueCharacteristics">Unique Characteristics</label>
                        <textarea id="uniqueCharacteristics" name="unique_characteristics" placeholder="What makes your coffee unique to your geographical location?"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="historicalSignificance">Historical Significance</label>
                        <textarea id="historicalSignificance" name="historical_significance" placeholder="Any historical or cultural significance of coffee farming in your area"></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Submit Application</button>
                </form>
            </div>
        </div>
        
        <div id="status" class="tab-content">
            <div class="card">
                <h2>Application Status</h2>
                <div class="form-group">
                    <label for="statusFarmerId">Enter Farmer ID:</label>
                    <input type="number" id="statusFarmerId" placeholder="Enter your Farmer ID">
                    <button type="button" class="btn" onclick="checkApplicationStatus()" style="margin-top: 0.5rem;">Check Status</button>
                </div>
                
                <div id="statusResults"></div>
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
            event.target.classList.add('active');
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
                resultsDiv.innerHTML = '<p>No applications found for this Farmer ID.</p>';
                return;
            }
            
            let html = '<h3>Your Applications:</h3>';
            
            applications.forEach(app => {
                const statusClass = app.status === 'approved' ? 'status-approved' : 
                                  app.status === 'rejected' ? 'status-rejected' : 'status-pending';
                
                html += `
                    <div class="application-item">
                        <h4>Application #${app.id} - ${app.variety_name}</h4>
                        <p><strong>Status:</strong> <span class="status-badge ${statusClass}">${app.status.toUpperCase()}</span></p>
                        <p><strong>Submitted:</strong> ${new Date(app.submission_date).toLocaleDateString()}</p>
                        <p><strong>Farm Location:</strong> ${app.farm_location}</p>
                        ${app.elevation ? `<p><strong>Elevation:</strong> ${app.elevation}m</p>` : ''}
                        ${app.reviewer_notes ? `<p><strong>Reviewer Notes:</strong> ${app.reviewer_notes}</p>` : ''}
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = html;
        }
        
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            
            const container = document.querySelector('.container');
            container.insertBefore(alertDiv, container.firstChild.nextSibling);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>
        '''
