-- =============================================================================
-- RUN ONCE on the PC that runs XAMPP (app + MySQL device) — phpMyAdmin or mysql CLI
-- NOT on admin web or client web PCs.
--
-- Allows admin web (device B) and client web (device C) to connect over Wi‑Fi/LAN.
-- After import, set the same user/password in:
--   Beanthentic/settings.json
--   Beanthentic-Client-Web/settings.json
--   connection.app_db_host = this PC's LAN IP (e.g. 192.168.x.x)
-- =============================================================================

USE mysql;

-- Remote user for LAN admin + client web (change password if you want)
CREATE USER IF NOT EXISTS 'beanthentic_remote'@'%' IDENTIFIED BY 'StrongPass123!';
GRANT ALL PRIVILEGES ON beanthentic_app.* TO 'beanthentic_remote'@'%';

-- Optional: allow root from LAN (dev only; less secure)
-- CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY '';
-- GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;

FLUSH PRIVILEGES;

-- Also on the XAMPP device:
-- 1) Edit C:\xampp\mysql\bin\my.ini → under [mysqld] set:  bind-address=0.0.0.0
-- 2) Restart MySQL in XAMPP Control Panel
-- 3) Windows Firewall: allow inbound TCP port 3306 on the XAMPP PC
