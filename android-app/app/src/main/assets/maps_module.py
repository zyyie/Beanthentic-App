from flask import render_template_string, jsonify
import csv
import os
import re


_BARANGAY_SKIP_KEYS = frozenset({
    "address (barangay)",
    "batangas",
    "lipa city",
    "c-leaseholder",
    "d-seasonal farm worker",
})

# CSV spellings / variants → official key in get_barangay_coordinates (one pin per barangay)
_BARANGAY_ALIASES_TO_OFFICIAL = {
    "pinagtong ulan": "Pinagtong-Ulan",
    "pinagtong-ulan": "Pinagtong-Ulan",
    "pinagtongulan": "Pinagtong-Ulan",
    "sto nino": "Santo Niño",
    "sto. nino": "Santo Niño",
    "mataas na kahoy": "Mataasnakahoy",
    "pag-olingin west": "Pagolingin",
    "pag olingin west": "Pagolingin",
    "sto. toribio": "Santo Toribio",
    "sto toribio": "Santo Toribio",
    "malagonlong": "Malagonlong",
    "rizal/ p. bata": "Rizal",
}


class MapsModule:
    def __init__(self, app):
        self.app = app
        self.setup_routes()
    
    def _barangay_match_key(self, s):
        s = (s or "").strip()
        s = re.sub(r"(?i)^barangay\s+", "", s)
        s = re.sub(r"\s+", " ", s)
        return s.lower()

    def _resolve_barangay(self, raw, barangay_coords):
        """Return (display_name, coords) using one canonical barangay key."""
        raw = (raw or "").strip()
        if not raw:
            return "Unknown", barangay_coords.get("Lipa City Proper", {"lat": 13.9414, "lng": 121.1605})
        key = self._barangay_match_key(raw)
        if key in _BARANGAY_SKIP_KEYS:
            return None, None
        official = _BARANGAY_ALIASES_TO_OFFICIAL.get(key)
        if official and official in barangay_coords:
            return official, barangay_coords[official]
        if raw in barangay_coords:
            return raw, barangay_coords[raw]
        for name in barangay_coords:
            if self._barangay_match_key(name) == key:
                return name, barangay_coords[name]
        disp = raw
        return disp, barangay_coords.get(disp, {"lat": 13.9414, "lng": 121.1605})

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
        module_dir = os.path.dirname(__file__)
        csv_candidates = [
            os.path.join(module_dir, 'coffee-database.csv'),
            os.path.join(os.path.dirname(module_dir), 'coffee-database.csv'),
            os.path.join(os.path.dirname(os.path.dirname(module_dir)), 'coffee-database.csv'),
            os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(module_dir))), 'coffee-database.csv'),
            os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(os.path.dirname(module_dir)))), 'coffee-database.csv'),
            os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(os.path.dirname(os.path.dirname(module_dir))))), 'coffee-database.csv'),
        ]
        csv_file_path = next((p for p in csv_candidates if os.path.exists(p)), csv_candidates[0])
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
                    
                    canon, coords = self._resolve_barangay(barangay, barangay_coords)
                    if canon is None:
                        continue
                    
                    barangay = canon
                    
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
            area = agg["total_area"]
            prod = agg["total_production"]
            desc = (
                f"Barangay {b} is a coffee-growing area in Lipa City. "
                f"Varieties grown here include {', '.join(varieties)}."
            )
            if area > 0:
                desc += f" Combined farm area (where reported): about {area:.2f} hectares."
            if prod > 0:
                desc += f" Approx. combined annual production (where reported): {prod:.0f} kg."
            n = agg["farm_count"]
            out.append({
                "id": i,
                "barangay": b,
                "latitude": agg["latitude"],
                "longitude": agg["longitude"],
                "farm_name": b,
                "farm_count": n,
                "varieties": varieties,
                "description": desc.strip(),
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
                        "Barangay Pinagtong-Ulan is a coffee-growing area in Lipa City. "
                        "Varieties grown here include Robusta."
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
                        "stroke": "#25671E",
                        "stroke-width": 2,
                        "stroke-opacity": 0.8,
                        "fill": "#25671E",
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
    <meta name="theme-color" content="#25671E">
    <meta name="color-scheme" content="light">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Coffee Farms Map - Lipa City | Beanthentic</title>
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    <style>
        :root {
            --brand: #25671E;
            --brand-dark: #1c5216;
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
            -webkit-tap-highlight-color: rgba(46, 111, 28, 0.14);
        }

        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
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
            border: 1px solid rgba(37, 103, 30, 0.2);
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
            background: linear-gradient(135deg, #25671E 0%, #1c5216 100%);
            color: white;
            padding: 1rem 0 1.15rem;
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
        .header-icon svg, .back-link svg, .sidebar h3 svg, .api-notice svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
        .header-icon svg { width: 2rem; height: 2rem; }
        
        .main-content {
            padding: 1.25rem 0 1.75rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--brand);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--brand-dark);
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
        
        /* Full-screen map layout (mobile-first) */
        body.has-app-bottom-nav {
            padding-bottom: 0 !important;
        }

        .map-screen {
            position: fixed;
            inset: 0;
            background: #f8fafc;
            z-index: 0;
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: calc(3.75rem + env(safe-area-inset-bottom, 0px));
            width: 100%;
        }

        .map-top-ui {
            position: absolute;
            top: calc(10px + env(safe-area-inset-top, 0px));
            left: max(12px, env(safe-area-inset-left, 0px));
            right: max(12px, env(safe-area-inset-right, 0px));
            z-index: 1080; /* above Leaflet tiles, below app bottom nav */
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            pointer-events: none;
        }

        .map-top-ui > * {
            pointer-events: auto;
        }

        .map-search {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(17, 24, 39, 0.12);
            backdrop-filter: saturate(160%) blur(12px);
            -webkit-backdrop-filter: saturate(160%) blur(12px);
            overflow: hidden;
        }

        .map-search-input {
            appearance: none;
            -webkit-appearance: none;
            width: 100%;
            padding: 10px 40px 10px 14px;
            border: none;
            outline: none;
            font-size: 0.95rem;
            background: transparent;
            color: #111827;
        }

        .map-search-input::placeholder {
            color: rgba(17, 24, 39, 0.45);
        }
        .map-search-input::-webkit-search-cancel-button,
        .map-search-input::-webkit-search-decoration,
        .map-search-input::-webkit-search-results-button,
        .map-search-input::-webkit-search-results-decoration {
            -webkit-appearance: none;
            appearance: none;
            display: none;
        }

        .map-search-clear {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            border-radius: 999px;
            border: none;
            background: rgba(17, 24, 39, 0.06);
            color: #111827;
            font-size: 1.25rem;
            line-height: 1;
            cursor: pointer;
        }

        .map-filters {
            display: flex;
            flex-wrap: nowrap;
            gap: 6px;
            width: 100%;
            max-width: 100%;
        }

        .chip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 34px;
            padding: 0 10px;
            flex: 1 1 0;
            min-width: 0;
            border-radius: 999px;
            border: 1px solid rgba(17, 24, 39, 0.10);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 8px 22px rgba(17, 24, 39, 0.10);
            font-size: 0.82rem;
            font-weight: 650;
            color: #1f2937;
            cursor: pointer;
            backdrop-filter: saturate(160%) blur(12px);
            -webkit-backdrop-filter: saturate(160%) blur(12px);
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        .chip.is-active {
            background: rgba(37, 103, 30, 0.95);
            border-color: rgba(37, 103, 30, 0.95);
            color: #ffffff;
            box-shadow: 0 12px 28px rgba(37, 103, 30, 0.22);
        }

        .chip-icon,
        .map-help-fab-icon {
            width: 18px;
            height: 18px;
            display: inline-block;
            background: center/contain no-repeat;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.12));
        }

        .chip.is-active .chip-icon {
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.18));
        }

        .map-help-fab {
            position: absolute;
            top: calc(92px + env(safe-area-inset-top, 0px));
            right: max(12px, env(safe-area-inset-right, 0px));
            z-index: 1080;
            width: 46px;
            height: 46px;
            border-radius: 999px;
            border: 1px solid rgba(17, 24, 39, 0.10);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 12px 30px rgba(17, 24, 39, 0.18);
            backdrop-filter: saturate(160%) blur(12px);
            -webkit-backdrop-filter: saturate(160%) blur(12px);
            cursor: pointer;
            display: grid;
            place-items: center;
            -webkit-tap-highlight-color: transparent;
        }
        .map-help-fab.is-opened {
            background: rgba(37, 103, 30, 0.95);
            border-color: rgba(37, 103, 30, 0.95);
            box-shadow: 0 12px 30px rgba(37, 103, 30, 0.28);
        }

        .map-sheet {
            position: absolute;
            left: max(12px, env(safe-area-inset-left, 0px));
            right: max(12px, env(safe-area-inset-right, 0px));
            bottom: calc(3.75rem + env(safe-area-inset-bottom, 0px) + 10px);
            z-index: 1090;
        }

        .map-sheet-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 18px 50px rgba(17, 24, 39, 0.22);
            border: 1px solid rgba(17, 24, 39, 0.08);
            overflow: hidden;
        }

        .map-sheet-header {
            padding: 12px 14px 10px;
            background: linear-gradient(145deg, #25671E 0%, #1c5216 100%);
            color: #ffffff;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .map-sheet-title {
            font-weight: 800;
            font-size: 1.02rem;
            line-height: 1.15;
        }

        .map-sheet-close {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            font-size: 1.35rem;
            line-height: 1;
            cursor: pointer;
        }

        .map-sheet-meta {
            padding: 10px 14px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .map-sheet-desc {
            padding: 10px 14px 12px;
            color: #1f2937;
            font-size: 0.9rem;
            line-height: 1.45;
        }

        .map-sheet-actions {
            padding: 12px 14px 14px;
            display: grid;
            gap: 10px;
            background: rgba(17, 24, 39, 0.02);
            border-top: 1px solid rgba(17, 24, 39, 0.06);
        }

        .map-sheet-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 44px;
            border-radius: 12px;
            font-weight: 750;
            font-size: 0.92rem;
            text-decoration: none !important;
            color: #ffffff !important;
            box-shadow: 0 10px 22px rgba(17, 24, 39, 0.14);
        }

        .map-sheet-btn--primary {
            background: #25671E;
        }

        .map-sheet-btn--secondary {
            background: #4b5563;
        }

        .map-guide {
            position: absolute;
            inset: 0;
            z-index: 1200;
            display: grid;
            place-items: center;
        }

        .map-guide[hidden],
        .map-sheet[hidden] {
            display: none !important;
        }

        .map-guide-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, 0.55);
        }

        .map-guide-card {
            position: relative;
            width: min(92vw, 360px);
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 22px 70px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            border: 1px solid rgba(17, 24, 39, 0.10);
        }

        .map-guide-title {
            background: linear-gradient(145deg, #25671E 0%, #1c5216 100%);
            color: #ffffff;
            font-weight: 850;
            padding: 12px 14px;
            font-size: 1rem;
        }

        .map-guide-body {
            padding: 14px 16px 12px;
            color: #111827;
            font-size: 0.98rem;
            line-height: 1.55;
            text-align: center;
        }

        .map-guide-actions {
            padding: 0 16px 16px;
        }

        .map-guide-btn {
            width: 100%;
            height: 44px;
            border-radius: 12px;
            border: none;
            background: #4b5563;
            color: #ffffff;
            font-weight: 800;
            font-size: 0.95rem;
            cursor: pointer;
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
            border-color: #25671E;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .farm-card.active {
            border-color: #25671E;
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
            box-shadow: 0 12px 32px rgba(37, 103, 30, 0.12);
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
            width: 14px;
            height: 18px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='18' viewBox='0 0 20 26'%3E%3Cpath fill='%23147539' stroke='%23ffffff' stroke-width='1.2' d='M10 1.2c-4.02 0-7.2 3.18-7.2 7.1 0 4.65 7.2 14.7 7.2 14.7s7.2-10.05 7.2-14.7c0-3.92-3.18-7.1-7.2-7.1z'/%3E%3Ccircle cx='10' cy='8.3' r='2.35' fill='%23ffffff'/%3E%3C/svg%3E") center/contain no-repeat;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.25));
        }
        
        #map {
            --popup-scale: 1;
        }
        
        .coffee-marker.leaflet-div-icon {
            background: transparent !important;
            border: none !important;
        }
        
        .barangay-pin-svg {
            display: block;
            line-height: 0;
            filter: drop-shadow(0 1px 3px rgba(0,0,0,0.35));
        }
        
        .beanthentic-popup .leaflet-popup-content-wrapper {
            padding: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 32px rgba(17, 24, 39, 0.2);
            max-width: min(calc(100vw - 24px), calc(200px + 130px * var(--popup-scale))) !important;
        }
        
        .beanthentic-popup .leaflet-popup-content {
            margin: 0;
            width: auto !important;
            min-width: 0;
        }
        
        /* Leaflet sets .leaflet-container a { color: #0078A8 } — higher specificity for action buttons */
        .leaflet-container .beanthentic-popup a.bp-btn,
        .leaflet-container .beanthentic-popup a.bp-btn:hover,
        .leaflet-container .beanthentic-popup a.bp-btn:visited,
        .leaflet-container .beanthentic-popup a.bp-btn:focus,
        .leaflet-container .beanthentic-popup a.bp-btn:active {
            color: #ffffff !important;
            text-decoration: none !important;
            -webkit-tap-highlight-color: transparent;
        }
        
        .leaflet-container .beanthentic-popup a.bp-btn-primary:hover {
            filter: brightness(1.08);
        }
        
        .leaflet-container .beanthentic-popup a.bp-btn-secondary:hover {
            filter: brightness(1.1);
        }
        
        .bp-popup {
            max-width: min(calc(100vw - 24px), calc(200px + 130px * var(--popup-scale)));
            box-sizing: border-box;
        }
        
        .bp-popup-header {
            background: linear-gradient(145deg, #25671E 0%, #25671E 100%);
            padding: calc(10px * var(--popup-scale)) calc(12px * var(--popup-scale));
        }
        
        .bp-popup-header h3 {
            margin: 0;
            font-size: calc(15px * var(--popup-scale));
            font-weight: 600;
            color: #ffffff !important;
            line-height: 1.25;
        }
        
        .bp-popup-header .bp-sub {
            margin: 5px 0 0 0;
            font-size: calc(11px * var(--popup-scale));
            color: rgba(248, 250, 252, 0.95) !important;
            line-height: 1.35;
        }
        
        .bp-badge {
            flex-shrink: 0;
            background: rgba(255,255,255,0.22);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: calc(9px * var(--popup-scale));
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #ffffff !important;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .bp-popup-body {
            padding: calc(8px * var(--popup-scale)) calc(10px * var(--popup-scale)) calc(10px * var(--popup-scale));
            background: #ffffff;
            max-height: min(42vh, calc(240px + 80px * var(--popup-scale)));
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }
        
        .bp-section {
            margin-bottom: calc(7px * var(--popup-scale));
        }
        
        .bp-section:last-of-type {
            margin-bottom: calc(5px * var(--popup-scale));
        }
        
        .bp-h4 {
            margin: 0 0 6px 0;
            font-size: calc(10px * var(--popup-scale));
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        
        .bp-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        
        .bp-tag {
            background: rgba(37, 103, 30, 0.12);
            color: #0f3d24;
            border: 1px solid rgba(37, 103, 30, 0.28);
            padding: 2px 8px;
            border-radius: 999px;
            font-size: calc(10px * var(--popup-scale));
            font-weight: 600;
        }
        
        .bp-about {
            margin: 0;
            color: #374151;
            font-size: calc(12px * var(--popup-scale));
            line-height: 1.5;
        }
        
        .bp-preview-wrap {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        
        .bp-preview-img {
            width: 100%;
            height: auto;
            max-height: 88px;
            object-fit: cover;
            display: block;
            background: #f3f4f6;
        }
        
        .bp-coords {
            background: #1f2937;
            color: #f9fafb;
            padding: 5px 8px;
            text-align: center;
            font-size: calc(10px * var(--popup-scale));
        }
        
        .bp-popup-footer {
            padding: 0 calc(10px * var(--popup-scale)) calc(10px * var(--popup-scale));
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
        }
        
        .bp-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        
        .bp-btn {
            flex: 1;
            min-width: 96px;
            text-align: center;
            text-decoration: none !important;
            padding: calc(8px * var(--popup-scale)) 10px;
            border-radius: 8px;
            font-size: calc(11px * var(--popup-scale));
            font-weight: 600;
            color: #ffffff !important;
            display: inline-block;
            box-sizing: border-box;
        }
        
        .bp-btn-primary {
            background: #25671E;
            color: #ffffff !important;
        }
        
        .bp-btn-secondary {
            background: #4b5563;
            color: #ffffff !important;
        }
        
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            height: 100%;
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
            border-top-color: #25671E;
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

        /* Match zoom control width with layer switcher width */
        .leaflet-right .leaflet-control-layers-toggle {
            width: 56px;
            height: 56px;
            background-size: 30px 30px;
        }
        .leaflet-right .leaflet-control-zoom {
            width: 56px;
        }
        .leaflet-right .leaflet-control-zoom a {
            width: 56px;
            height: 42px;
            line-height: 42px;
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
        }
        @media (max-width: 768px) {
            .map-top-ui {
                top: calc(10px + env(safe-area-inset-top, 0px));
            }
            .map-help-fab {
                top: calc(96px + env(safe-area-inset-top, 0px));
            }
        }
        @media (max-width: 480px) {
            .chip {
                font-size: 0.8rem;
                gap: 6px;
                padding: 0 8px;
            }
            .map-filters {
                gap: 6px;
            }
            .chip-icon,
            .map-help-fab-icon {
                width: 16px;
                height: 16px;
            }
            .map-sheet {
                left: 10px;
                right: 10px;
            }
        }
    </style>
</head>
<body class="has-app-bottom-nav">
    <div class="map-screen" role="application" aria-label="Beanthentic Map">
        <div id="map" class="loading" aria-label="Map">
            <i data-lucide="loader-2"></i>
            <span>Loading map…</span>
        </div>

        <div class="map-top-ui" role="region" aria-label="Map controls">
            <div class="map-search">
                <label for="mapSearchInput" class="sr-only">Search barangay</label>
                <input
                    id="mapSearchInput"
                    class="map-search-input"
                    type="search"
                    placeholder="Search barangay"
                    autocomplete="off"
                    inputmode="search"
                    enterkeyhint="search"
                />
                <button id="mapSearchClear" class="map-search-clear" type="button" aria-label="Clear search" hidden>×</button>
            </div>
            <div class="map-filters" role="tablist" aria-label="Coffee variety filter">
                <button class="chip is-active" type="button" data-variety="Liberica" role="tab" aria-selected="true">
                    <span class="chip-icon" aria-hidden="true"></span>
                    Liberica
                </button>
                <button class="chip" type="button" data-variety="Robusta" role="tab" aria-selected="false">
                    <span class="chip-icon" aria-hidden="true"></span>
                    Robusta
                </button>
                <button class="chip" type="button" data-variety="Excelsa" role="tab" aria-selected="false">
                    <span class="chip-icon" aria-hidden="true"></span>
                    Excelsa
                </button>
            </div>
        </div>

        <button id="mapHelpFab" class="map-help-fab" type="button" aria-label="How to use the Beanthentic Map">
            <span class="map-help-fab-icon" aria-hidden="true"></span>
        </button>

        <section id="mapSheet" class="map-sheet" aria-label="Selected barangay details" hidden>
            <div class="map-sheet-card">
                <div class="map-sheet-header">
                    <div class="map-sheet-title" id="sheetTitle">Barangay</div>
                    <button id="mapSheetClose" class="map-sheet-close" type="button" aria-label="Close details">×</button>
                </div>
                <div class="map-sheet-meta" id="sheetMeta"></div>
                <div class="map-sheet-desc" id="sheetDesc"></div>
                <div class="map-sheet-actions">
                    <a id="sheetStreetView" class="map-sheet-btn map-sheet-btn--primary" href="#" target="_blank" rel="noopener noreferrer">Google Street View</a>
                    <a id="sheetGoogleMaps" class="map-sheet-btn map-sheet-btn--secondary" href="#" target="_blank" rel="noopener noreferrer">Google Maps View</a>
                </div>
            </div>
        </section>

        <section id="mapList" class="map-list" aria-label="Barangay list">
            <div class="map-list-inner">
                <div class="map-list-title">Barangays</div>
                <div id="farmList" class="farm-list">
                    <div class="loading">
                        <i data-lucide="loader-2"></i>
                        <span>Loading farms…</span>
                    </div>
                </div>
            </div>
        </section>

        <div id="mapGuide" class="map-guide" role="dialog" aria-modal="true" aria-labelledby="guideTitle" hidden>
            <div class="map-guide-backdrop" data-guide-close="true"></div>
            <div class="map-guide-card">
                <div class="map-guide-title" id="guideTitle">How to use the Beanthentic Map?</div>
                <div class="map-guide-body" id="guideBody">
                    use two fingers to zoom the map.<br/>
                    Scroll the barangay list below the map;<br/>
                    the row you tap highlights in green.
                </div>
                <div class="map-guide-actions">
                    <button id="guideNext" class="map-guide-btn" type="button">Next &gt;</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let map;
        let markers = [];
        let farms = [];
        let selectedFarm = null;
        let activeVariety = 'Liberica';
        let searchQuery = '';
        let markerLayer = null;
        let lipaBoundaryLayer = null;
        let guideStep = 0;

        function escapeHtml(s) {
            if (s == null || s === undefined) return '';
            const d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }
        
        function toDataUri(svg) {
            return 'data:image/svg+xml,' + encodeURIComponent(svg.trim());
        }

        function setBeanIcons() {
            const beanSvg = (fill) => `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M14.8 3.5c-3.7 0-7.3 3.2-8 7.5-.7 4.3 1.6 9.5 6.1 9.5 3.8 0 7.3-3.1 8-7.5.7-4.3-1.6-9.5-6.1-9.5Z" fill="${fill}" stroke="rgba(0,0,0,0.18)" stroke-width="0.8"/>
                    <path d="M12.6 5.3c-1.8 2.5-2.7 5.1-2.8 7.8-.1 2.6.5 4.7 1.7 6.4" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
            `;
            const colors = {
                Liberica: '#25671E',
                Robusta: '#0f766e',
                Excelsa: '#8B4513'
            };
            document.querySelectorAll('.chip').forEach(btn => {
                const v = btn.getAttribute('data-variety');
                const icon = btn.querySelector('.chip-icon');
                if (icon && colors[v]) icon.style.backgroundImage = `url("${toDataUri(beanSvg(colors[v]))}")`;
            });
            const help = document.querySelector('.map-help-fab-icon');
            if (help) {
                help.style.backgroundImage = "url('/beantHentic_logo.png')";
                const logoProbe = new Image();
                logoProbe.onerror = function() {
                    help.style.backgroundImage = `url("${toDataUri(beanSvg('#8B4513'))}")`;
                };
                logoProbe.src = '/beantHentic_logo.png';
            }
        }

        function createBarangayPinIcon() {
            const html = '<div class="barangay-pin-svg" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="26" viewBox="0 0 20 26"><path fill="#25671E" stroke="#ffffff" stroke-width="1.25" d="M10 1.25c-4.04 0-7.25 3.21-7.25 7.15 0 4.7 7.25 14.85 7.25 14.85s7.25-10.15 7.25-14.85c0-3.94-3.21-7.15-7.25-7.15z"/><circle cx="10" cy="8.35" r="2.4" fill="#fff"/></svg></div>';
            return L.divIcon({
                html,
                className: 'coffee-marker',
                iconSize: [20, 26],
                iconAnchor: [10, 26]
            });
        }
        
        function refreshIcons(root) {
            if (window.lucide) lucide.createIcons({ attrs: { 'stroke-width': 2 }, root: root || document.body });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            refreshIcons();
            setBeanIcons();
            wireMapUi();
            loadCoffeeFarms();
        });

        function wireMapUi() {
            const searchInput = document.getElementById('mapSearchInput');
            const clearBtn = document.getElementById('mapSearchClear');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    searchQuery = (searchInput.value || '').trim();
                    if (clearBtn) clearBtn.hidden = !searchQuery;
                    renderFarms();
                });
            }
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchQuery = '';
                    if (searchInput) searchInput.value = '';
                    clearBtn.hidden = true;
                    renderFarms();
                });
            }

            document.querySelectorAll('.chip').forEach(btn => {
                btn.addEventListener('click', function() {
                    const v = btn.getAttribute('data-variety');
                    if (!v) return;
                    activeVariety = v;
                    document.querySelectorAll('.chip').forEach(b => {
                        const on = b === btn;
                        b.classList.toggle('is-active', on);
                        b.setAttribute('aria-selected', on ? 'true' : 'false');
                    });
                    renderFarms(true);
                });
            });

            const fab = document.getElementById('mapHelpFab');
            if (fab) {
                fab.addEventListener('click', openGuide);
            }

            const guide = document.getElementById('mapGuide');
            if (guide) {
                guide.addEventListener('click', function(e) {
                    const t = e.target;
                    if (t && t.getAttribute && t.getAttribute('data-guide-close') === 'true') closeGuide();
                });
            }

            const next = document.getElementById('guideNext');
            if (next) next.addEventListener('click', advanceGuide);

            const sheetClose = document.getElementById('mapSheetClose');
            if (sheetClose) sheetClose.addEventListener('click', closeSheet);
        }

        function openGuide() {
            guideStep = 0;
            renderGuide();
            const guide = document.getElementById('mapGuide');
            if (guide) guide.hidden = false;
            const fab = document.getElementById('mapHelpFab');
            if (fab) fab.classList.add('is-opened');
        }

        function closeGuide() {
            const guide = document.getElementById('mapGuide');
            if (guide) guide.hidden = true;
            const fab = document.getElementById('mapHelpFab');
            if (fab) fab.classList.remove('is-opened');
            try { localStorage.setItem('beanthentic_map_guide_seen', '1'); } catch (_e) {}
        }

        function maybeOpenGuide() {
            try {
                if (!localStorage.getItem('beanthentic_map_guide_seen')) {
                    openGuide();
                }
            } catch (_e) {
                openGuide();
            }
        }

        function advanceGuide() {
            guideStep += 1;
            if (guideStep >= 2) {
                closeGuide();
                return;
            }
            renderGuide();
        }

        function renderGuide() {
            const body = document.getElementById('guideBody');
            const next = document.getElementById('guideNext');
            if (!body || !next) return;
            if (guideStep === 0) {
                body.innerHTML = 'Use two fingers to zoom the map.<br/>Scroll the barangay list below the map;<br/>the row you tap highlights in green.';
                next.textContent = 'Next >';
            } else {
                body.innerHTML = 'Tap a marker to see barangay details.<br/>Use Liberica / Robusta / Excelsa filters<br/>to show the coffee varieties.';
                next.textContent = 'Done';
            }
        }
        
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
                                addCoffeeMarkers();
                                refreshIcons();
                                maybeOpenGuide();
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
                    window.lipaCityBoundary = boundary;
                    renderLipaCityBoundary();
                })
                .catch(error => {
                    console.log('Could not load city boundary:', error);
                });
        }

        function boundaryStyle(feature) {
            return {
                color: feature.properties.stroke,
                weight: feature.properties['stroke-width'],
                opacity: feature.properties['stroke-opacity'],
                fillColor: feature.properties.fill,
                fillOpacity: feature.properties['fill-opacity']
            };
        }

        function renderLipaCityBoundary() {
            if (!map || !window.lipaCityBoundary) return;
            if (lipaBoundaryLayer) {
                map.removeLayer(lipaBoundaryLayer);
            }
            lipaBoundaryLayer = L.geoJSON(window.lipaCityBoundary, {
                style: boundaryStyle
            }).addTo(map);
        }
        
        function addCoffeeMarkers() {
            renderMarkers();
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
            refreshIcons();
            addCoffeeMarkers();
            maybeOpenGuide();
            renderLipaCityBoundary();
        }
        
        function initializeMap(center) {
            const el = document.getElementById('map');
            el.classList.remove('loading');
            el.innerHTML = '';
            map = L.map('map', { zoomControl: false }).setView([center.lat, center.lng], 12);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors | Beanthentic Coffee',
                maxZoom: 19
            }).addTo(map);

            // Add Humanitarian layer and switcher above zoom controls
            const googleStreets = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors, Tiles style by Humanitarian OpenStreetMap Team',
                maxZoom: 19
            });

            const baseMaps = {
                "Default": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors | Beanthentic Coffee',
                    maxZoom: 19
                }),
                "Humanitarian": googleStreets
            };

            // Zoom control bottom-right (mobile friendly)
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            // Layer switcher above zoom control
            L.control.layers(baseMaps, null, {
                position: 'bottomright'
            }).addTo(map);
            
            // Add scale control
            L.control.scale({
                position: 'bottomleft',
                metric: true,
                imperial: false
            }).addTo(map);

            markerLayer = L.layerGroup().addTo(map);
            renderLipaCityBoundary();
        }
        
        function populateFarmList() {
            const farmList = document.getElementById('farmList');
            if (!farmList) return;
            renderFarms();
        }

        function getFilteredFarms() {
            const q = (searchQuery || '').toLowerCase();
            return (farms || []).filter(f => {
                const vOk = (f.varieties || []).includes(activeVariety);
                if (!vOk) return false;
                if (!q) return true;
                const name = (f.farm_name || f.barangay || '').toLowerCase();
                return name.includes(q);
            });
        }

        function renderFarms(keepSelection) {
            renderFarmList(keepSelection);
            renderMarkers(keepSelection);
            if (!keepSelection) closeSheet();
        }

        function renderFarmList(keepSelection) {
            const farmList = document.getElementById('farmList');
            if (!farmList) return;
            const filtered = getFilteredFarms();
            if (!filtered.length) {
                farmList.innerHTML = '<div style="padding:10px;color:#6b7280;font-size:0.9rem">No barangays found.</div>';
                return;
            }
            const farmCards = filtered.map(farm => `
                <div class="farm-card" onclick="selectFarm(${farm.id})" id="farm-${farm.id}">
                    <div class="farm-name">Barangay ${escapeHtml(farm.farm_name)}</div>
                    <div class="farm-varieties">
                        ${(farm.varieties || []).map(v => `<span class="variety-tag">${escapeHtml(v)}</span>`).join('')}
                    </div>
                </div>
            `).join('');
            farmList.innerHTML = farmCards;

            if (keepSelection && selectedFarm) {
                const card = document.getElementById(`farm-${selectedFarm.id}`);
                if (card) card.classList.add('selected-farm');
            }
        }

        function renderMarkers(keepSelection) {
            if (!map || !markerLayer) return;
            const filtered = getFilteredFarms();
            const coffeeIcon = createBarangayPinIcon();
            markerLayer.clearLayers();
            markers = [];
            filtered.forEach(farm => {
                const marker = L.marker([farm.latitude, farm.longitude], { icon: coffeeIcon });
                marker.on('click', () => selectFarm(farm.id));
                marker.farmId = farm.id;
                marker.addTo(markerLayer);
                markers.push(marker);
            });

            if (keepSelection && selectedFarm) {
                const stillThere = filtered.some(f => f.id === selectedFarm.id);
                if (!stillThere) closeSheet();
            }
        }
        
        function selectFarm(farmId) {
            // Remove previous selection
            document.querySelectorAll('.farm-card').forEach(card => {
                card.classList.remove('selected-farm');
            });
            
            // Add selection to new farm (sidebar only)
            selectedFarm = (farms || []).find(farm => farm.id === farmId);
            if (selectedFarm) {
                // Highlight sidebar
                const sidebarCard = document.getElementById(`farm-${farmId}`);
                if (sidebarCard) {
                    sidebarCard.classList.add('selected-farm');
                    sidebarCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Center map on selected farm
                map.setView([selectedFarm.latitude, selectedFarm.longitude], 15);
                openSheet(selectedFarm);
            }
        }

        function openSheet(farm) {
            const sheet = document.getElementById('mapSheet');
            if (!sheet || !farm) return;
            const title = document.getElementById('sheetTitle');
            const meta = document.getElementById('sheetMeta');
            const desc = document.getElementById('sheetDesc');
            const aStreet = document.getElementById('sheetStreetView');
            const aMaps = document.getElementById('sheetGoogleMaps');

            const lat = farm.latitude;
            const lng = farm.longitude;
            if (title) title.textContent = `Barangay ${farm.farm_name || farm.barangay || ''}`;
            if (meta) {
                meta.innerHTML = (farm.varieties || []).map(v => `<span class="bp-tag">${escapeHtml(v)}</span>`).join('');
            }
            if (desc) desc.textContent = farm.description || '';
            if (aStreet) aStreet.href = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${lat},${lng}`;
            if (aMaps) aMaps.href = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
            sheet.hidden = false;
        }

        function closeSheet() {
            const sheet = document.getElementById('mapSheet');
            if (sheet) sheet.hidden = true;
        }
        
        function showError(message) {
            const mapElement = document.getElementById('map');
            mapElement.className = 'loading';
            mapElement.innerHTML = '<span style="color:#dc2626;font-size:2rem;line-height:1">⚠</span><p style="color:#b91c1c;text-align:center;max-width:280px;padding:0 12px">' + escapeHtml(message) + '</p>';
        }
    </script>

    <nav class="app-bottom-nav" aria-label="Quick navigation">
        <div class="app-bottom-nav-inner">
            <a href="/#home" class="app-bottom-nav-link">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </span>
                <span class="app-bottom-nav-label">Home</span>
            </a>
            <a href="/about.php" class="app-bottom-nav-link">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </span>
                <span class="app-bottom-nav-label">About</span>
            </a>
            <a href="/register-farm" class="app-bottom-nav-link app-bottom-nav-link--featured">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </span>
                <span class="app-bottom-nav-label">Register</span>
            </a>
            <a href="/maps" class="app-bottom-nav-link is-active" aria-current="page">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
                </span>
                <span class="app-bottom-nav-label">Map</span>
            </a>
            <a href="/login.php" id="nav-signin" class="app-bottom-nav-link app-bottom-nav-link--signin" data-no-loader="true">
                <span class="app-bottom-nav-icon-wrap" aria-hidden="true">
                    <svg class="app-bottom-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <span class="app-bottom-nav-label">Sign in</span>
            </a>
        </div>
    </nav>
    <script>
        (function () {
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
                document.addEventListener('DOMContentLoaded', syncBottomNavAccount);
            } else {
                syncBottomNavAccount();
            }

            window.addEventListener('storage', function (e) {
                if (!e || e.key === 'beanthentic_user') syncBottomNavAccount();
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

