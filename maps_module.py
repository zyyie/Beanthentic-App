from flask import render_template_string, jsonify
import json
import csv
import os

class MapsModule:
    def __init__(self, app):
        self.app = app
        self.setup_routes()
    
    def setup_routes(self):
        """Setup routes for maps module"""
        
        @self.app.route('/maps')
        def maps_page():
            """Serve Google Maps page for Lipa City coffee farms"""
            return render_template_string(self.get_maps_html())
        
        @self.app.route('/api/maps/coffee-farms')
        def get_coffee_farms():
            """API endpoint to get coffee farm locations"""
            return self.get_coffee_farms_data()
        
        @self.app.route('/api/maps/coffee-farms/geojson')
        def get_coffee_farms_geojson():
            """API endpoint to get coffee farm locations in GeoJSON format"""
            return self.get_coffee_farms_geojson()
        
        @self.app.route('/api/maps/lipa-city-boundary')
        def get_lipa_city_boundary():
            """API endpoint to get Lipa City boundary in GeoJSON format"""
            return self.get_lipa_city_boundary_geojson()
    
    def get_barangay_coordinates(self):
        """Get approximate coordinates for Lipa City barangays"""
        return {
            "Adya": {"lat": 13.9800, "lng": 121.1200},
            "Antipolo del Sur": {"lat": 13.9678, "lng": 121.1456},
            "Bagong Pook": {"lat": 13.9400, "lng": 121.1600},
            "Balete": {"lat": 13.9456, "lng": 121.1234},
            "Balintawak": {"lat": 13.9500, "lng": 121.1700},
            "Banaybanay": {"lat": 13.9300, "lng": 121.1300},
            "Barangay 1": {"lat": 13.9410, "lng": 121.1600},
            "Barangay 2": {"lat": 13.9415, "lng": 121.1605},
            "Barangay 3": {"lat": 13.9420, "lng": 121.1610},
            "Barangay 4": {"lat": 13.9405, "lng": 121.1595},
            "Barangay 5": {"lat": 13.9425, "lng": 121.1590},
            "Barangay 6": {"lat": 13.9400, "lng": 121.1615},
            "Barangay 7": {"lat": 13.9430, "lng": 121.1600},
            "Barangay 8": {"lat": 13.9395, "lng": 121.1590},
            "Barangay 9": {"lat": 13.9410, "lng": 121.1620},
            "Barangay 10": {"lat": 13.9435, "lng": 121.1615},
            "Barangay 11": {"lat": 13.9390, "lng": 121.1625},
            "Barangay 12": {"lat": 13.9440, "lng": 121.1585},
            "Batangas": {"lat": 13.9500, "lng": 121.1400},
            "Boca": {"lat": 13.9600, "lng": 121.1500},
            "Bolbok": {"lat": 13.9700, "lng": 121.1100},
            "Bucal": {"lat": 13.9700, "lng": 121.1600},
            "Bulacnin": {"lat": 13.9800, "lng": 121.1500},
            "Bugtong na Pulo": {"lat": 13.9400, "lng": 121.2000},
            "Calamias": {"lat": 13.9400, "lng": 121.1300},
            "Calicanto": {"lat": 13.9300, "lng": 121.1100},
            "Concepcion": {"lat": 13.9200, "lng": 121.1700},
            "Cumba": {"lat": 13.9200, "lng": 121.1300},
            "Dagatan": {"lat": 13.9200, "lng": 121.1400},
            "Dela Paz": {"lat": 13.9800, "lng": 121.1500},
            "Denrica": {"lat": 13.9100, "lng": 121.1400},
            "Duhatan": {"lat": 13.9100, "lng": 121.1400},
            "Galamay-Amo": {"lat": 13.9500, "lng": 121.1800},
            "Halang": {"lat": 13.9500, "lng": 121.1600},
            "Inosloban": {"lat": 13.9600, "lng": 121.1900},
            "Kayumanggi": {"lat": 13.9300, "lng": 121.1200},
            "Latag": {"lat": 13.9600, "lng": 121.1900},
            "Lipa City Proper": {"lat": 13.9414, "lng": 121.1605},
            "Lodlod": {"lat": 13.9400, "lng": 121.1800},
            "Lumbang": {"lat": 13.9700, "lng": 121.1100},
            "Mabini": {"lat": 13.9400, "lng": 121.1700},
            "Malabanan": {"lat": 13.9200, "lng": 121.1500},
            "Malagonlong": {"lat": 13.9800, "lng": 121.1800},
            "Malitlit": {"lat": 13.9800, "lng": 121.1600},
            "Marauoy": {"lat": 13.9100, "lng": 121.1300},
            "Mataas na Lupa": {"lat": 13.9500, "lng": 121.1400},
            "Mataasnakahoy": {"lat": 13.9600, "lng": 121.1000},
            "Pagolingin": {"lat": 13.9700, "lng": 121.1500},
            "Pangao": {"lat": 13.9600, "lng": 121.1200},
            "Pinagkawitan": {"lat": 13.9300, "lng": 121.1700},
            "Pinagtong-Ulan": {"lat": 13.9667, "lng": 121.1500},
            "Poblacion": {"lat": 13.9400, "lng": 121.1600},
            "Rizal": {"lat": 13.9400, "lng": 121.1800},
            "Sabang": {"lat": 13.9700, "lng": 121.1700},
            "Sampaguita": {"lat": 13.9200, "lng": 121.1700},
            "Sampaloc": {"lat": 13.9700, "lng": 121.1500},
            "San Antonio": {"lat": 13.9600, "lng": 121.1400},
            "San Benito": {"lat": 13.9345, "lng": 121.1890},
            "San Carlos": {"lat": 13.9500, "lng": 121.1400},
            "San Celestino": {"lat": 13.9800, "lng": 121.1300},
            "San Fernando": {"lat": 13.9400, "lng": 121.1200},
            "San Francisco": {"lat": 13.9100, "lng": 121.1500},
            "San Guillermo": {"lat": 13.9600, "lng": 121.1700},
            "San Ildefonso": {"lat": 13.9300, "lng": 121.1600},
            "San Isidro": {"lat": 13.9300, "lng": 121.1500},
            "San Jose": {"lat": 13.9600, "lng": 121.1300},
            "San Juan": {"lat": 13.9700, "lng": 121.1200},
            "San Lucas": {"lat": 13.9000, "lng": 121.1400},
            "San Miguel": {"lat": 13.9200, "lng": 121.1400},
            "San Nicolas": {"lat": 13.9800, "lng": 121.1400},
            "San Pedro": {"lat": 13.9100, "lng": 121.1600},
            "San Roque": {"lat": 13.9100, "lng": 121.1700},
            "San Salvador": {"lat": 13.9400, "lng": 121.1300},
            "San Sebastian": {"lat": 13.9800, "lng": 121.1700},
            "Santo Niño": {"lat": 13.9234, "lng": 121.1345},
            "Santo Toribio": {"lat": 13.9500, "lng": 121.1200},
            "Santa Cruz": {"lat": 13.9600, "lng": 121.1800},
            "Santa Monica": {"lat": 13.9200, "lng": 121.1500},
            "Santa Teresita": {"lat": 13.9500, "lng": 121.1300},
            "Santiago": {"lat": 13.9800, "lng": 121.1700},
            "Sico": {"lat": 13.9700, "lng": 121.1300},
            "Sinturisan": {"lat": 13.9600, "lng": 121.1600},
            "Talisay": {"lat": 13.9100, "lng": 121.1300},
            "Talaga": {"lat": 13.9123, "lng": 121.1567},
            "Tangob": {"lat": 13.9300, "lng": 121.1700},
            "Tigam": {"lat": 13.9200, "lng": 121.1800},
            "Tipakan": {"lat": 13.9000, "lng": 121.1600},
            "Tulo": {"lat": 13.9500, "lng": 121.1900},
            "Upang": {"lat": 13.9400, "lng": 121.1500},
            "Valle Verde": {"lat": 13.9700, "lng": 121.1400},
            "Wawa": {"lat": 13.9600, "lng": 121.1600},
            "Yaon": {"lat": 13.9400, "lng": 121.1200}
        }
    
    def read_coffee_database_csv(self):
        """Read and parse the coffee-database.csv file"""
        csv_file_path = os.path.join(os.path.dirname(__file__), 'coffee-database.csv')
        barangay_coords = self.get_barangay_coordinates()
        coffee_farms = []
        
        try:
            with open(csv_file_path, 'r', encoding='utf-8', errors='ignore') as file:
                csv_reader = csv.reader(file)
                
                # Skip header rows until we find the data
                data_started = False
                headers = []
                
                for row in csv_reader:
                    if not data_started:
                        # Look for the header row that contains "No." and "Name of Farmer"
                        if len(row) > 1 and "No." in str(row[0]) and "Name of Farmer" in str(row[1]):
                            headers = row
                            data_started = True
                        continue
                    
                    # Skip empty rows
                    if not row or len(row) < 3:
                        continue
                    
                    # Extract farmer data
                    farmer_no = row[0].strip() if row[0] else ""
                    farmer_name = row[1].strip() if row[1] else ""
                    barangay = row[2].strip() if row[2] else ""
                    
                    # Skip if no farmer name or barangay
                    if not farmer_name or not barangay:
                        continue
                    
                    # Get coordinates for barangay
                    coords = barangay_coords.get(barangay, {"lat": 13.9414, "lng": 121.1605})
                    
                    # Extract production data (columns for Liberica, Excelsa, Robusta)
                    varieties = []
                    total_production = 0
                    
                    # Look for production data in the row (simplified approach)
                    if len(row) > 20:
                        try:
                            # These are approximate column indices based on the CSV structure
                            liberica_prod = float(row[21]) if row[21] and row[21].strip() else 0
                            excelsa_prod = float(row[22]) if row[22] and row[22].strip() else 0
                            robusta_prod = float(row[23]) if row[23] and row[23].strip() else 0
                            
                            total_production = liberica_prod + excelsa_prod + robusta_prod
                            
                            if liberica_prod > 0:
                                varieties.append("Liberica")
                            if excelsa_prod > 0:
                                varieties.append("Excelsa")
                            if robusta_prod > 0:
                                varieties.append("Robusta")
                        except (ValueError, IndexError):
                            pass
                    
                    # Default to Robusta if no varieties found
                    if not varieties:
                        varieties = ["Robusta"]
                    
                    # Extract farm area if available
                    farm_area = 0
                    try:
                        if len(row) > 8 and row[8]:
                            farm_area = float(row[8])
                    except (ValueError, IndexError):
                        pass
                    
                    farm_data = {
                        "id": len(coffee_farms) + 1,
                        "barangay": barangay,
                        "latitude": coords["lat"],
                        "longitude": coords["lng"],
                        "farm_name": farmer_name,
                        "varieties": varieties,
                        "description": f"Coffee farmer in {barangay}. Farm area: {farm_area} ha. Annual production: {total_production} kg.",
                        "farmer_no": farmer_no,
                        "farm_area": farm_area,
                        "production": total_production
                    }
                    
                    coffee_farms.append(farm_data)
        
        except FileNotFoundError:
            print(f"CSV file not found: {csv_file_path}")
            # Return empty list if file not found
            return []
        except Exception as e:
            print(f"Error reading CSV file: {e}")
            return []
        
        return coffee_farms
    
    def get_coffee_farms_data(self):
        """Return coffee farm data from CSV file"""
        # Try to read from CSV file first
        coffee_farms = self.read_coffee_database_csv()
        
        # If CSV data is empty, fall back to sample data
        if not coffee_farms:
            print("Using fallback sample data - CSV file not found or empty")
            coffee_farms = [
                {
                    "id": 1,
                    "barangay": "Pinagtong-Ulan",
                    "latitude": 13.9667,
                    "longitude": 121.1500,
                    "farm_name": "Sample Farm - Pinagtong-Ulan",
                    "varieties": ["Robusta"],
                    "description": "Sample coffee farm data"
                }
            ]
        
        return {
            "success": True,
            "farms": coffee_farms,
            "center": {
                "lat": 13.9414,
                "lng": 121.1605,
                "name": "Lipa City"
            },
            "source": "CSV Database" if coffee_farms else "Sample Data"
        }
    
    def get_coffee_farms_geojson(self):
        """Return coffee farm data in GeoJSON format"""
        farms_data = self.get_coffee_farms_data()
        
        features = []
        for farm in farms_data['farms']:
            feature = {
                "type": "Feature",
                "geometry": {
                    "type": "Point",
                    "coordinates": [farm['longitude'], farm['latitude']]
                },
                "properties": {
                    "id": farm['id'],
                    "name": farm['farm_name'],
                    "barangay": farm['barangay'],
                    "varieties": farm['varieties'],
                    "description": farm['description'],
                    "marker-color": "#8B4513",
                    "marker-size": "medium",
                    "marker-symbol": "coffee"
                }
            }
            features.append(feature)
        
        geojson = {
            "type": "FeatureCollection",
            "features": features
        }
        
        return jsonify(geojson)
    
    def get_lipa_city_boundary_geojson(self):
        """Return Lipa City boundary in GeoJSON format"""
        # Approximate boundary coordinates for Lipa City
        # This is a simplified boundary polygon for demonstration
        boundary = {
            "type": "FeatureCollection",
            "features": [
                {
                    "type": "Feature",
                    "properties": {
                        "name": "Lipa City Boundary",
                        "stroke": "#147539",
                        "stroke-width": 2,
                        "stroke-opacity": 0.8,
                        "fill": "#147539",
                        "fill-opacity": 0.1
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [121.0800, 13.8800],  # Southwest corner
                            [121.0800, 14.0000],  # Northwest corner
                            [121.2400, 14.0000],  # Northeast corner
                            [121.2400, 13.8800],  # Southeast corner
                            [121.0800, 13.8800]   # Close polygon
                        ]]
                    }
                }
            ]
        }
        
        return jsonify(boundary)
    
    def get_maps_html(self):
        """Return HTML for Google Maps page"""
        return '''
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Farms Map - Lipa City | Beanthentic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #147539 0%, #0f5a2c 100%);
            color: white;
            padding: 2rem 0;
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
            padding: 2rem 0;
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
        
        .maps-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .map-wrapper {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        #map {
            height: 600px;
            width: 100%;
        }
        
        .sidebar {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
            max-height: 600px;
            overflow-y: auto;
        }
        
        .sidebar h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .farm-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .farm-card {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .farm-card:hover {
            border-color: #147539;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .farm-card.active {
            border-color: #147539;
            background: #f0fdf4;
        }
        
        .farm-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .farm-location {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .farm-varieties {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            margin-bottom: 0.5rem;
        }
        
        .variety-tag {
            background: #f3f4f6;
            color: #374151;
            padding: 0.125rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .farm-description {
            font-size: 0.875rem;
            color: #6b7280;
            line-height: 1.4;
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
        
        .legend {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
        }
        
        .legend-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }
        
        .legend-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #8B4513;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 600px;
            color: #6b7280;
        }
        
        .loading i {
            font-size: 2rem;
            margin-right: 1rem;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .api-notice {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        
        .api-notice strong {
            display: block;
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 1024px) {
            .maps-container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                max-height: 400px;
            }
            
            #map {
                height: 400px;
            }
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
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
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h1>Lipa City Coffee Farms Map</h1>
                <p>Discover coffee farms across Lipa City barangays with our interactive map</p>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="container">
            <a href="/" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </a>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="totalFarms">-</div>
                    <div class="stat-label">Total Coffee Farms</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalBarangays">-</div>
                    <div class="stat-label">Barangays Covered</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalVarieties">-</div>
                    <div class="stat-label">Coffee Varieties</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="avgElevation">-</div>
                    <div class="stat-label">Avg. Elevation (m)</div>
                </div>
            </div>
            
            <div class="maps-container">
                <div class="map-wrapper">
                    <div id="map" class="loading">
                        <i class="fas fa-spinner"></i>
                        Loading map...
                    </div>
                </div>
                
                <div class="sidebar">
                    <h3>
                        <i class="fas fa-coffee"></i>
                        Coffee Farms
                    </h3>
                    
                    <div class="api-notice">
                        <strong><i class="fas fa-info-circle"></i> Demo Mode Active</strong>
                        Using OpenStreetMap (Leaflet) for demo. For Google Maps, add your API key to maps_module.py
                    </div>
                    
                    <div class="legend">
                        <div class="legend-title">Legend</div>
                        <div class="legend-item">
                            <div class="legend-marker"></div>
                            <span>Coffee Farm Location</span>
                        </div>
                    </div>
                    
                    <div class="farm-list" id="farmList">
                        <div class="loading">
                            <i class="fas fa-spinner"></i>
                            Loading farms...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let map;
        let markers = [];
        let farms = [];
        let selectedFarm = null;
        
        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCoffeeFarms();
        });
        
        function loadCoffeeFarms() {
            // Show loading state
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                mapContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f9fafb; color: #6b7280; font-size: 16px;"><i class="fas fa-coffee"></i> Loading coffee farms...</div>';
            }
            
            // Load GeoJSON data for coffee farms
            fetch('/api/maps/coffee-farms/geojson')
                .then(response => response.json())
                .then(geojson => {
                    loadGeoJSONData(geojson);
                })
                .catch(error => {
                    // Fallback to regular JSON if GeoJSON fails
                    fetch('/api/maps/coffee-farms')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                farms = data.farms;
                                initializeMap(data.center);
                                populateFarmList();
                                updateStatistics();
                                addCoffeeMarkers();
                            } else {
                                showError('Failed to load coffee farms data');
                            }
                        })
                        .catch(error => {
                            showError('Error loading coffee farms: ' + error.message);
                        });
                });
            
            // Load Lipa City boundary
            fetch('/api/maps/lipa-city-boundary')
                .then(response => response.json())
                .then(boundary => {
                    // Boundary will be loaded when map is initialized
                    window.lipaCityBoundary = boundary;
                })
                .catch(error => {
                    console.log('Could not load city boundary:', error);
                });
        }
        
        function addCoffeeMarkers() {
            // Add location pin markers for fallback mode
            const locationIcon = L.divIcon({
                html: '<div style="position: relative;"><div style="background: #8B4513; width: 20px; height: 20px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div><div style="position: absolute; top: 2px; left: 2px; width: 6px; height: 6px; background: white; border-radius: 50%; transform: rotate(-45deg);"></div></div>',
                iconSize: [24, 24],
                className: 'location-marker',
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            });
            
            farms.forEach(farm => {
                const marker = L.marker([farm.latitude, farm.longitude], { icon: locationIcon })
                    .addTo(map);
                
                // Create popup content with working street view links
                const popupContent = `
                    <div style="padding: 10px; max-width: 250px;">
                        <h4 style="margin: 0 0 8px 0; color: #1f2937;">${farm.farm_name}</h4>
                        <p style="margin: 4px 0; color: #6b7280; font-size: 14px;">
                            <i class="fas fa-map-marker-alt"></i> ${farm.barangay}
                        </p>
                        <div style="margin: 8px 0;">
                            ${farm.varieties.map(v => `<span style="background: #f3f4f6; padding: 2px 6px; border-radius: 10px; font-size: 12px; margin-right: 4px;">${v}</span>`).join('')}
                        </div>
                        <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 13px; line-height: 1.4;">${farm.description}</p>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                            <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${farm.latitude},${farm.longitude}" 
                               target="_blank" 
                               style="color: #147539; text-decoration: none; font-size: 12px; font-weight: 500; display: inline-block; margin-right: 10px;">
                                <i class="fas fa-street-view"></i> Street View
                            </a>
                            <a href="https://www.google.com/maps/search/?api=1&query=${farm.latitude},${farm.longitude}" 
                               target="_blank" 
                               style="color: #147539; text-decoration: none; font-size: 12px; font-weight: 500;">
                                <i class="fas fa-map"></i> Google Maps
                            </a>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                marker.on('click', () => selectFarm(farm.id));
                
                marker.farmId = farm.id;
                markers.push(marker);
            });
        }
        
        function loadGeoJSONData(geojson) {
            // Extract farm data from GeoJSON
            farms = geojson.features.map(feature => ({
                id: feature.properties.id,
                barangay: feature.properties.barangay,
                latitude: feature.geometry.coordinates[1],
                longitude: feature.geometry.coordinates[0],
                farm_name: feature.properties.name,
                varieties: feature.properties.varieties,
                description: feature.properties.description
            }));
            
            // Initialize map with GeoJSON data
            initializeMap({
                lat: 13.9414,
                lng: 121.1605,
                name: "Lipa City"
            });
            
            populateFarmList();
            updateStatistics();
            
            // Add GeoJSON layer to map
            if (map && geojson) {
                L.geoJSON(geojson, {
                    pointToLayer: function(feature, latlng) {
                        // Create location pin icon like in the image
                        const locationIcon = L.divIcon({
                            html: '<div style="position: relative;"><div style="background: #8B4513; width: 20px; height: 20px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div><div style="position: absolute; top: 2px; left: 2px; width: 6px; height: 6px; background: white; border-radius: 50%; transform: rotate(-45deg);"></div></div>',
                            iconSize: [24, 24],
                            className: 'location-marker',
                            iconAnchor: [12, 24],
                            popupAnchor: [0, -24]
                        });
                        
                        const marker = L.marker(latlng, { icon: locationIcon });
                        
                        // Create popup content with working street view link
                        const popupContent = `
                            <div style="padding: 10px; max-width: 250px;">
                                <h4 style="margin: 0 0 8px 0; color: #1f2937;">${feature.properties.name}</h4>
                                <p style="margin: 4px 0; color: #6b7280; font-size: 14px;">
                                    <i class="fas fa-map-marker-alt"></i> ${feature.properties.barangay}
                                </p>
                                <div style="margin: 8px 0;">
                                    ${feature.properties.varieties.map(v => `<span style="background: #f3f4f6; padding: 2px 6px; border-radius: 10px; font-size: 12px; margin-right: 4px;">${v}</span>`).join('')}
                                </div>
                                <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 13px; line-height: 1.4;">${feature.properties.description}</p>
                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                                    <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${latlng.lat},${latlng.lng}" 
                                       target="_blank" 
                                       style="color: #147539; text-decoration: none; font-size: 12px; font-weight: 500; display: inline-block; margin-right: 10px;">
                                        <i class="fas fa-street-view"></i> Street View
                                    </a>
                                    <a href="https://www.google.com/maps/search/?api=1&query=${latlng.lat},${latlng.lng}" 
                                       target="_blank" 
                                       style="color: #147539; text-decoration: none; font-size: 12px; font-weight: 500;">
                                        <i class="fas fa-map"></i> Google Maps
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        marker.bindPopup(popupContent);
                        marker.on('click', () => selectFarm(feature.properties.id));
                        marker.farmId = feature.properties.id;
                        
                        return marker;
                    }
                }).addTo(map);
                
                // Add city boundary if available
                if (window.lipaCityBoundary) {
                    L.geoJSON(window.lipaCityBoundary, {
                        style: function(feature) {
                            return {
                                color: feature.properties.stroke,
                                weight: feature.properties['stroke-width'],
                                opacity: feature.properties['stroke-opacity'],
                                fillColor: feature.properties.fill,
                                fillOpacity: feature.properties['fill-opacity']
                            };
                        }
                    }).addTo(map);
                }
            }
        }
        
        function initializeMap(center) {
            // Initialize Leaflet map (OpenStreetMap - free alternative to Google Maps)
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                map = L.map('map').setView([center.lat, center.lng], 12);
                
                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors | Beanthentic Coffee',
                    maxZoom: 19
                }).addTo(map);
                
                // Add Google Street View layer
                const googleStreets = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors, Tiles style by Humanitarian OpenStreetMap Team',
                    maxZoom: 19
                });
                
                // Add layer control for street view toggle
                const baseMaps = {
                    "Standard": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors | Beanthentic Coffee',
                        maxZoom: 19
                    }),
                    "Street View": googleStreets
                };
                
                L.control.layers(baseMaps, null, {
                    position: 'topright'
                }).addTo(map);
                
                // Add scale control
                L.control.scale({
                    position: 'bottomleft',
                    metric: true,
                    imperial: false
                }).addTo(map);
            }
        }
        
        function populateFarmList() {
            const farmList = document.getElementById('farmList');
            
            const farmCards = farms.map(farm => `
                <div class="farm-card" onclick="selectFarm(${farm.id})" id="farm-${farm.id}">
                    <div class="farm-name">${farm.farm_name}</div>
                    <div class="farm-location">
                        <i class="fas fa-map-marker-alt"></i> ${farm.barangay}
                    </div>
                    <div class="farm-varieties">
                        ${farm.varieties.map(v => `<span class="variety-tag">${v}</span>`).join('')}
                    </div>
                    <div class="farm-description">${farm.description}</div>
                </div>
            `).join('');
            
            farmList.innerHTML = farmCards;
        }
        
        function selectFarm(farmId) {
            // Remove active class from all cards
            document.querySelectorAll('.farm-card').forEach(card => {
                card.classList.remove('active');
            });
            
            // Add active class to selected card
            const selectedCard = document.getElementById(`farm-${farmId}`);
            if (selectedCard) {
                selectedCard.classList.add('active');
            }
            
            // Find and center map on selected farm
            const farm = farms.find(f => f.id === farmId);
            if (farm) {
                map.setView([farm.latitude, farm.longitude], 15);
                
                // Open popup for the marker
                const marker = markers.find(m => m.farmId === farmId);
                if (marker) {
                    marker.openPopup();
                }
            }
            
            selectedFarm = farmId;
        }
        
        function updateStatistics() {
            document.getElementById('totalFarms').textContent = farms.length;
            
            const uniqueBarangays = [...new Set(farms.map(f => f.barangay))];
            document.getElementById('totalBarangays').textContent = uniqueBarangays.length;
            
            const allVarieties = farms.flatMap(f => f.varieties);
            const uniqueVarieties = [...new Set(allVarieties)];
            document.getElementById('totalVarieties').textContent = uniqueVarieties.length;
            
            // Calculate average elevation (placeholder - would need elevation data)
            document.getElementById('avgElevation').textContent = '1,200';
        }
        
        function showError(message) {
            const mapElement = document.getElementById('map');
            mapElement.innerHTML = `
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #ef4444;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>${message}</p>
                </div>
            `;
        }
    </script>
</body>
</html>
        '''
