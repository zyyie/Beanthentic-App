-- LUMANG database lang (may data na). Bagong install: gamitin xampp-beanthentic-import.sql (kasama na lahat).
-- Run once on XAMPP (phpMyAdmin) — client web reports → admin Client Report module.
USE beanthentic_app;

CREATE TABLE IF NOT EXISTS client_misconduct_report (
  report_id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reporter_name          VARCHAR(255) NOT NULL,
  reporter_contact       VARCHAR(255) NOT NULL DEFAULT '',
  reason_category        VARCHAR(255) NOT NULL,
  reason_detail          VARCHAR(255) NOT NULL DEFAULT '',
  allegation             TEXT NOT NULL,
  chat_json              TEXT NULL,
  farmer_id              BIGINT UNSIGNED NULL,
  farmer_no              VARCHAR(50) NULL,
  farmer_name            VARCHAR(255) NOT NULL DEFAULT '',
  status                 VARCHAR(40) NOT NULL DEFAULT 'under review',
  INDEX idx_cmr_status (status),
  INDEX idx_cmr_created (created_at),
  INDEX idx_cmr_farmer (farmer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
