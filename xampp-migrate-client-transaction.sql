-- LUMANG database lang (may data na). Bagong install: gamitin xampp-beanthentic-import.sql (kasama na lahat).
-- Run once on XAMPP (phpMyAdmin) so Client Web saves ALL form fields.
USE beanthentic_app;

ALTER TABLE customer_transaction
  ADD COLUMN transaction_type VARCHAR(40) NOT NULL DEFAULT 'pickup' AFTER reference_no,
  ADD COLUMN pickup_date DATE NULL AFTER transaction_date,
  ADD COLUMN pickup_date_display VARCHAR(32) NULL AFTER pickup_date,
  ADD COLUMN valid_id_path VARCHAR(500) NULL AFTER pickup_date_display,
  ADD COLUMN valid_id_filename VARCHAR(255) NULL AFTER valid_id_path,
  ADD COLUMN quantity_unit VARCHAR(20) NOT NULL DEFAULT 'KG' AFTER quantity,
  ADD COLUMN submitted_from VARCHAR(40) NOT NULL DEFAULT 'client_web' AFTER valid_id_filename,
  ADD COLUMN client_form_json TEXT NULL AFTER submitted_from;
