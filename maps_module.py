from flask import render_template_string, jsonify
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
                        "barangay": barangay,
                        "latitude": coords["lat"],
                        "longitude": coords["lng"],
                        "varieties": varieties,
                        "farm_area": farm_area,
                        "production": total_production,
                    }
                    
                    coffee_farms.append(farm_data)
        
        except FileNotFoundError:
            print(f"CSV file not found: {csv_file_path}")
            # Return empty list if file not found
            return []
        except Exception as e:
            print(f"Error reading CSV file: {e}")
            return []
        
        return self._aggregate_by_barangay(coffee_farms)
    
    def _aggregate_by_barangay(self, rows):
        """One map point per barangay—no individual farmer names (privacy & clarity)."""
        by = {}
        for f in rows:
            b = (f.get("barangay") or "").strip()
            if not b:
                continue
            if b not in by:
                by[b] = {
                    "barangay": b,
                    "latitude": f["latitude"],
                    "longitude": f["longitude"],
                    "varieties": set(),
                    "farm_count": 0,
                    "total_area": 0.0,
                    "total_production": 0.0,
                }
            agg = by[b]
            agg["farm_count"] += 1
            for v in f.get("varieties") or []:
                agg["varieties"].add(v)
            try:
                agg["total_area"] += float(f.get("farm_area") or 0)
            except (TypeError, ValueError):
                pass
            try:
                agg["total_production"] += float(f.get("production") or 0)
            except (TypeError, ValueError):
                pass
        out = []
        for i, b in enumerate(sorted(by.keys()), start=1):
            agg = by[b]
            varieties = sorted(agg["varieties"]) or ["Robusta"]
            n = agg["farm_count"]
            area = agg["total_area"]
            prod = agg["total_production"]
            desc = (
                f"Barangay {b} — coffee-growing area in Lipa City. "
                f"This pin shows where to go; the dataset lists {n} farm record(s) here. "
                f"Varieties reported: {', '.join(varieties)}. "
            )
            if area > 0:
                desc += f"Combined farm area (where reported): about {area:.2f} hectares. "
            if prod > 0:
                desc += f"Approx. combined annual production (where reported): {prod:.0f} kg. "
            desc += "Individual farmer names are not shown on the map."
            out.append({
                "id": i,
                "barangay": b,
                "latitude": agg["latitude"],
                "longitude": agg["longitude"],
                "farm_name": b,
                "farm_count": n,
                "varieties": varieties,
                "description": desc,
            })
        return out
    
    def get_coffee_farms_data(self):
        """Return barangay-level points (one per barangay) from CSV."""
        coffee_farms = self.read_coffee_database_csv()
        
        if not coffee_farms:
            print("Using fallback sample data - CSV file not found or empty")
            coffee_farms = [
                {
                    "id": 1,
                    "barangay": "Pinagtong-Ulan",
                    "latitude": 13.9667,
                    "longitude": 121.1500,
                    "farm_name": "Pinagtong-Ulan",
                    "farm_count": 1,
                    "varieties": ["Robusta"],
                    "description": (
                        "Barangay Pinagtong-Ulan — sample location. "
                        "Individual farmer names are not shown on the map."
                    ),
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
                    "name": farm['barangay'],
                    "barangay": farm['barangay'],
                    "farm_count": farm.get('farm_count', 1),
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#147539">
    <meta name="color-scheme" content="light">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Coffee Farms Map - Lipa City | Beanthentic</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    <style>
        :root {
            --brand: #147539;
            --brand-dark: #0f5a2c;
            --surface: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --accent: #8B4513;
            --radius: 16px;
            --shadow: 0 10px 40px rgba(17, 24, 39, 0.08);
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
            color: var(--text);
            background: linear-gradient(165deg, #f0fdf4 0%, #e8eef5 45%, #dbeafe 100%);
            line-height: 1.6;
            min-height: 100vh;
            min-height: 100dvh;
            padding: max(0px, env(safe-area-inset-top)) max(0px, env(safe-area-inset-right)) max(0px, env(safe-area-inset-bottom)) max(0px, env(safe-area-inset-left));
            -webkit-tap-highlight-color: rgba(20, 117, 57, 0.12);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding-left: max(16px, env(safe-area-inset-left));
            padding-right: max(16px, env(safe-area-inset-right));
        }
        .map-app-intro { margin-bottom: 1.25rem; }
        .map-lead {
            font-size: 0.95rem;
            color: #374151;
            line-height: 1.55;
            max-width: 48rem;
            margin-bottom: 1rem;
        }
        .map-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .map-detail-tile {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            font-size: 0.8125rem;
            color: #4b5563;
            line-height: 1.45;
        }
        .map-detail-tile strong {
            display: block;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--brand);
            margin-bottom: 0.35rem;
        }
        .map-hint-bar {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 1px solid rgba(20, 117, 57, 0.2);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.8125rem;
            color: #065f46;
            margin-bottom: 1.25rem;
        }
        .map-hint-bar svg {
            width: 1.1rem;
            height: 1.1rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }
        .stat-hint {
            display: block;
            font-size: 0.7rem;
            font-weight: 500;
            color: #9ca3af;
            margin-top: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
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
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
            color: #86efac;
        }
        .header-icon svg, .back-link svg, .sidebar h3 svg, .api-notice svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
        .header-icon svg { width: 3rem; height: 3rem; }
        
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
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid rgba(17, 24, 39, 0.06);
        }
        
        #map {
            height: 600px;
            width: 100%;
        }
        
        .sidebar {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.25rem 1.5rem;
            border: 1px solid rgba(17, 24, 39, 0.06);
            max-height: min(600px, 70vh);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
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
            background: var(--surface);
            padding: 1.35rem 1.25rem;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(17, 24, 39, 0.06);
            border: 1px solid rgba(17, 24, 39, 0.05);
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(20, 117, 57, 0.12);
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
        
        .coffee-marker {
            background: #8B4513;
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            width: 28px;
            height: 28px;
        }
        
        .coffee-marker .cup {
            color: white;
            font-size: 13px;
            line-height: 1;
        }
        
        .selected-farm {
            background: #147539 !important;
            color: white !important;
            border: 2px solid #147539 !important;
        }
        
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            height: 600px;
            color: #6b7280;
            font-size: 0.95rem;
        }
        .loading svg {
            width: 1.5rem;
            height: 1.5rem;
            animation: spin 0.9s linear infinite;
        }
        .map-load-spin {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 3px solid #e5e7eb;
            border-top-color: #147539;
            animation: spin 0.85s linear infinite;
            flex-shrink: 0;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .loading i {
            font-size: 2rem;
            margin-right: 1rem;
            animation: none;
        }
        
        .api-notice {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #92400e;
            padding: 0.9rem 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-size: 0.8125rem;
            line-height: 1.45;
        }
        .api-notice .notice-title {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            color: #78350f;
        }
        
        @media (max-width: 1024px) {
            .maps-container {
                grid-template-columns: 1fr;
            }
            .sidebar {
                max-height: min(420px, 55vh);
            }
            #map, .loading {
                height: min(420px, 50vh);
                min-height: 280px;
            }
        }
        @media (max-width: 768px) {
            .header { padding: 1.5rem 0; }
            .header h1 {
                font-size: clamp(1.35rem, 5vw, 2rem);
                padding: 0 0.25rem;
            }
            .header p { font-size: 0.95rem; }
            .main-content { padding: 1.25rem 0 2rem; }
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.75rem;
            }
            .stat-number { font-size: 1.5rem; }
        }
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .map-detail-grid {
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
                    <i data-lucide="map-pin"></i>
                </div>
                <h1>Lipa City Coffee Farms Map</h1>
                <p>Tap the map or list to open farm details, varieties, and quick links—like a native maps app.</p>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <div class="container">
            <a href="/" class="back-link">
                <i data-lucide="arrow-left"></i>
                Back to Home
            </a>

            <div class="map-app-intro">
                <p class="map-lead">
                    Each <strong>pin is one barangay</strong> (neighborhood)—not individual farms. Names of farmers are <strong>not</strong> shown; you only see where coffee is grown so you can plan visits and directions safely and privately.
                </p>
                <div class="map-detail-grid">
                    <div class="map-detail-tile">
                        <strong>Interact</strong>
                        Pan and zoom the map, switch base layers (top-right), and tap any marker for farm name, varieties, and external map links.
                    </div>
                    <div class="map-detail-tile">
                        <strong>List</strong>
                        Scroll the farm cards on the right (below the map on phones). Selecting a card centers the map and opens the popup.
                    </div>
                    <div class="map-detail-tile">
                        <strong>Data</strong>
                        Counts and varieties are combined per barangay from the project file. One marker per barangay keeps the map simple and protects farmer privacy.
                    </div>
                </div>
                <div class="map-hint-bar">
                    <i data-lucide="smartphone"></i>
                    <span><strong>Mobile:</strong> use two fingers to zoom the map. Scroll the barangay list below the map; the row you tap highlights in green.</span>
                </div>
            </div>
            
            <div class="stats-grid">
                    <div class="stat-card">
                    <div class="stat-number" id="totalFarms">-</div>
                    <div class="stat-label">Barangays on map</div>
                    <span class="stat-hint">One pin each</span>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalBarangays">-</div>
                    <div class="stat-label">Farm records (file)</div>
                    <span class="stat-hint">All rows summed</span>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalVarieties">-</div>
                    <div class="stat-label">Coffee Varieties</div>
                    <span class="stat-hint">Distinct tags</span>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="avgElevation">-</div>
                    <div class="stat-label">Region elevation</div>
                    <span class="stat-hint">Typical Batangas highland</span>
                </div>
            </div>
            
            <div class="maps-container">
                <div class="map-wrapper">
                    <div id="map" class="loading">
                        <i data-lucide="loader-2"></i>
                        <span>Loading map…</span>
                    </div>
                </div>
                
                <div class="sidebar">
                    <h3>
                        <i data-lucide="map-pin"></i>
                        Barangays (Lipa City)
                    </h3>
                    
                    <div class="api-notice">
                        <div class="notice-title"><i data-lucide="info"></i> Map data</div>
                        Base map and tiles are served via OpenStreetMap contributors. Popups link to Google Maps for directions and Street View where available.
                    </div>
                    
                    <div class="legend">
                        <div class="legend-title">Legend</div>
                        <div class="legend-item">
                            <div class="legend-marker"></div>
                            <span>Barangay (one pin each)</span>
                        </div>
                    </div>
                    
                    <div class="farm-list" id="farmList">
                        <div class="loading">
                            <i data-lucide="loader-2"></i>
                            <span>Loading farms…</span>
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

        function escapeHtml(s) {
            if (s == null || s === undefined) return '';
            const d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }
        function refreshIcons(root) {
            if (window.lucide) lucide.createIcons({ attrs: { 'stroke-width': 2 }, root: root || document.body });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            refreshIcons();
            loadCoffeeFarms();
        });
        
        function loadCoffeeFarms() {
            // Show loading state
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                mapContainer.className = 'loading';
                mapContainer.innerHTML = '<span class="map-load-spin" aria-hidden="true"></span><span>Loading coffee farms…</span>';
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
                                refreshIcons();
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
            // Add coffee cup markers for fallback mode
            const coffeeIcon = L.divIcon({
                html: '<div style="background: #8B4513; color: white; width: 28px; height: 28px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center;"><span class="cup" style="font-size:14px;line-height:1">☕</span></div>',
                iconSize: [28, 28],
                className: 'coffee-marker',
                iconAnchor: [14, 14],
                popupAnchor: [0, -14]
            });
            
            farms.forEach(farm => {
                const marker = L.marker([farm.latitude, farm.longitude], { icon: coffeeIcon })
                    .addTo(map);
                
                // Create popup content with working street view links
                const popupContent = `
                    <div style="padding: 10px; max-width: 250px;">
                        <h4 style="margin: 0 0 8px 0; color: #1f2937;">${escapeHtml(farm.farm_name)}</h4>
                        <p style="margin: 4px 0; color: #6b7280; font-size: 14px;">
                            📍 ${escapeHtml(farm.barangay)}
                        </p>
                        <div style="margin: 8px 0;">
                            ${farm.varieties.map(v => `<span style="background: #f3f4f6; padding: 2px 6px; border-radius: 10px; font-size: 12px; margin-right: 4px;">${escapeHtml(v)}</span>`).join('')}
                        </div>
                        <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 13px; line-height: 1.4;">${escapeHtml(farm.description)}</p>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                            <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${farm.latitude},${farm.longitude}" 
                               target="_blank" rel="noopener noreferrer"
                               style="color: #147539; text-decoration: none; font-size: 12px; font-weight: 500; display: inline-block; margin-right: 10px;">
                                Street View
                            </a>
                            <a href="https://www.google.com/maps/search/?api=1&query=${farm.latitude},${farm.longitude}" 
                               target="_blank" rel="noopener noreferrer"
                               style="color: #147539; text-decoration: none; font-size: 12px; font-weight: 500;">
                                Open in Maps
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
                farm_count: feature.properties.farm_count || 1,
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
            refreshIcons();
            
            // Add GeoJSON layer to map
            if (map && geojson) {
                L.geoJSON(geojson, {
                    pointToLayer: function(feature, latlng) {
                        // Create coffee bean/leaf icon
                        const coffeeIcon = L.divIcon({
                            html: '<div style="background: #8B4513; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 12px; position: relative;"><div style="background: #654321; width: 12px; height: 12px; border-radius: 50%; position: absolute; top: 6px; left: 6px;"></div><div style="background: #228B22; width: 8px; height: 8px; border-radius: 50%; position: absolute; top: 8px; left: 8px;"></div></div>',
                            iconSize: [28, 28],
                            className: 'coffee-marker',
                            iconAnchor: [14, 14],
                            popupAnchor: [0, -14]
                        });
                        
                        const marker = L.marker(latlng, { icon: coffeeIcon });
                        
                        // Create compact professional popup content
                        const pname = escapeHtml(feature.properties.name);
                        const pbar = escapeHtml(feature.properties.barangay);
                        const fc = feature.properties.farm_count || 1;
                        const pdesc = escapeHtml(feature.properties.description);
                        const staticMap = 'https://staticmap.openstreetmap.de/staticmap.php?center=' + latlng.lat + ',' + latlng.lng + '&zoom=15&size=240x128&maptype=mapnik';
                        const popupContent = `
                            <div style="padding: 0; max-width: 268px; font-family: 'DM Sans', system-ui, sans-serif;">
                                <div style="background: linear-gradient(135deg, #8B4513 0%, #654321 100%); color: white; padding: 12px 16px; border-radius: 8px 8px 0 0;">
                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;">
                                        <div>
                                            <h3 style="margin: 0; font-size: 16px; font-weight: 600;">${pname}</h3>
                                            <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.95;">📍 ${pbar} · ${fc} farm record(s) in data</p>
                                        </div>
                                        <span style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Barangay</span>
                                    </div>
                                </div>
                                <div style="padding: 14px 16px 16px;">
                                    <div style="margin-bottom: 12px;">
                                        <h4 style="margin: 0 0 6px 0; font-size: 12px; font-weight: 600; color: #374151; text-transform: uppercase; letter-spacing: 0.04em;">Varieties</h4>
                                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                            ${feature.properties.varieties.map(v => `
                                                <span style="background: #147539; color: white; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">
                                                    ${escapeHtml(v)}
                                                </span>
                                            `).join('')}
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 12px;">
                                        <h4 style="margin: 0 0 6px 0; font-size: 12px; font-weight: 600; color: #374151;">About</h4>
                                        <p style="margin: 0; color: #6b7280; font-size: 12px; line-height: 1.45;">${pdesc}</p>
                                    </div>
                                    <div style="margin-bottom: 12px;">
                                        <h4 style="margin: 0 0 6px 0; font-size: 12px; font-weight: 600; color: #374151;">Preview</h4>
                                        <div style="border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb;">
                                            <img src="${staticMap}" alt="Map preview" width="240" height="128" style="width: 100%; height: auto; display: block; object-fit: cover;" loading="lazy">
                                            <div style="background: #1f2937; color: white; padding: 6px 8px; text-align: center; font-size: 11px;">
                                                ${latlng.lat.toFixed(5)}, ${latlng.lng.toFixed(5)}
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: stretch;">
                                        <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${latlng.lat},${latlng.lng}" target="_blank" rel="noopener noreferrer"
                                           style="flex: 1; min-width: 120px; text-align: center; background: #147539; color: white; text-decoration: none; padding: 10px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                            Street View
                                        </a>
                                        <a href="https://www.google.com/maps/search/?api=1&query=${latlng.lat},${latlng.lng}" target="_blank" rel="noopener noreferrer"
                                           style="flex: 1; min-width: 120px; text-align: center; background: #4b5563; color: white; text-decoration: none; padding: 10px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                            Google Maps
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        marker.bindPopup(popupContent);
                        marker.on('click', () => selectFarm(feature.properties.id));
                        marker.farmId = feature.properties.id;
                        markers.push(marker);
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
            const el = document.getElementById('map');
            el.classList.remove('loading');
            el.innerHTML = '';
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
                "Default": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors | Beanthentic Coffee',
                    maxZoom: 19
                }),
                "Humanitarian": googleStreets
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
        
        function populateFarmList() {
            const farmList = document.getElementById('farmList');
            const farmCards = farms.map(farm => `
                <div class="farm-card" onclick="selectFarm(${farm.id})" id="farm-${farm.id}">
                    <div class="farm-name">Barangay ${escapeHtml(farm.farm_name)}</div>
                    <div class="farm-location">${farm.farm_count || 1} farm record(s) · Lipa City</div>
                    <div class="farm-varieties">
                        ${farm.varieties.map(v => `<span class="variety-tag">${escapeHtml(v)}</span>`).join('')}
                    </div>
                    <div class="farm-description">${escapeHtml(farm.description)}</div>
                </div>
            `).join('');
            farmList.innerHTML = farmCards;
        }
        
        function selectFarm(farmId) {
            // Remove previous selection
            document.querySelectorAll('.farm-card').forEach(card => {
                card.classList.remove('selected-farm');
            });
            
            // Add selection to new farm (sidebar only)
            selectedFarm = farms.find(farm => farm.id === farmId);
            if (selectedFarm) {
                // Highlight sidebar
                const sidebarCard = document.getElementById(`farm-${farmId}`);
                if (sidebarCard) {
                    sidebarCard.classList.add('selected-farm');
                    sidebarCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Center map on selected farm
                map.setView([selectedFarm.latitude, selectedFarm.longitude], 15);
                
                // Open popup
                markers.forEach(marker => {
                    if (marker.farmId === farmId) {
                        marker.openPopup();
                    }
                });
            }
        }
        
        function updateStatistics() {
            document.getElementById('totalFarms').textContent = farms.length;
            const totalRecords = farms.reduce(function(s, f) { return s + (f.farm_count || 1); }, 0);
            document.getElementById('totalBarangays').textContent = totalRecords;
            
            const allVarieties = farms.flatMap(f => f.varieties);
            const uniqueVarieties = [...new Set(allVarieties)];
            document.getElementById('totalVarieties').textContent = uniqueVarieties.length;
            
            // Calculate average elevation (placeholder - would need elevation data)
            document.getElementById('avgElevation').textContent = '1,200';
        }
        
        function showError(message) {
            const mapElement = document.getElementById('map');
            mapElement.className = 'loading';
            mapElement.innerHTML = '<span style="color:#dc2626;font-size:2rem;line-height:1">⚠</span><p style="color:#b91c1c;text-align:center;max-width:280px;padding:0 12px">' + escapeHtml(message) + '</p>';
        }
    </script>
</body>
</html>
        '''
