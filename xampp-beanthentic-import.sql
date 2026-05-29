CREATE DATABASE IF NOT EXISTS beanthentic_app
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE beanthentic_app;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS document_analysis;
DROP TABLE IF EXISTS activity_log_entry;
DROP TABLE IF EXISTS admin_user;
DROP TABLE IF EXISTS farmer_moderation_logs;
DROP TABLE IF EXISTS account_settings;
DROP TABLE IF EXISTS social;
DROP TABLE IF EXISTS gi_updates;
DROP TABLE IF EXISTS gi_farmers_contribution;
DROP TABLE IF EXISTS farmer_notification;
DROP TABLE IF EXISTS shared_messages;
DROP TABLE IF EXISTS farmer_message;
DROP TABLE IF EXISTS client_misconduct_report;
DROP TABLE IF EXISTS transaction_history;
DROP TABLE IF EXISTS customer_transaction;
DROP TABLE IF EXISTS production_information;
DROP TABLE IF EXISTS tree_counts;
DROP TABLE IF EXISTS affiliation_information;
DROP TABLE IF EXISTS farm_information;
DROP TABLE IF EXISTS personal_information;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS farmers;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Login/signup use: phone_number (E.164 +639…), optional email, username = display full name.
CREATE TABLE users (
  user_id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  phone_number       VARCHAR(32) NOT NULL COMMENT 'E.164 e.g. +639XXXXXXXXX',
  email              VARCHAR(191) NULL DEFAULT NULL,
  username           VARCHAR(150) NULL COMMENT 'Display / full name',
  password_hash      VARCHAR(255) NOT NULL,
  role               ENUM('farmer','client') NOT NULL DEFAULT 'farmer',
  is_active          TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at      DATETIME NULL,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_phone (phone_number),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE farmers (
  farmer_id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id            BIGINT UNSIGNED NOT NULL UNIQUE,
  farm_code          VARCHAR(50) NULL UNIQUE,
  profile_photo      VARCHAR(255) NULL COMMENT 'Web path e.g. /uploads/farmers/farmer_1.jpg (file under assets/uploads/farmers/)',
  status             ENUM('pending','active','inactive') NOT NULL DEFAULT 'pending',
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  -- Admin moderation (warning / suspend)
  is_suspended       TINYINT(1) NOT NULL DEFAULT 0,
  suspended_until    DATETIME NULL,
  suspension_reason  VARCHAR(500) NULL,
  warning_count      INT NOT NULL DEFAULT 0,
  last_warning_at    DATETIME NULL,
  last_warning_reason VARCHAR(500) NULL,
  -- Optional denormalized reverse references (for easier viewing in phpMyAdmin)
  personal_info_id BIGINT UNSIGNED NULL,
  affiliation_info_id BIGINT UNSIGNED NULL,
  farm_info_id BIGINT UNSIGNED NULL,
  latest_tree_count_id BIGINT UNSIGNED NULL,
  latest_production_info_id BIGINT UNSIGNED NULL,
  CONSTRAINT fk_farmers_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE personal_information (
  personal_info_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id              BIGINT UNSIGNED NOT NULL UNIQUE,
  first_name             VARCHAR(100) NULL,
  last_name              VARCHAR(100) NULL,
  birthday               DATE NULL,
  contact_number         VARCHAR(20) NULL,
  province               VARCHAR(100) NULL,
  municipality           VARCHAR(100) NULL,
  barangay               VARCHAR(150) NULL,
  current_address        VARCHAR(255) NULL,
  created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_personal_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE client (
  client_id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id             BIGINT UNSIGNED NOT NULL UNIQUE,
  company_name        VARCHAR(150) NULL,
  full_name           VARCHAR(150) NULL,
  contact_number      VARCHAR(20) NULL,
  email               VARCHAR(191) NULL,
  address             VARCHAR(255) NULL,
  report              TEXT NULL,
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_client_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Admin web login (Beanthentic dashboard)
CREATE TABLE admin_user (
  id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  phone_number    VARCHAR(255) NOT NULL,
  full_name       VARCHAR(255) NOT NULL,
  password_hash   VARCHAR(512) NOT NULL,
  created_at      DATETIME NOT NULL,
  UNIQUE KEY uq_admin_user_phone (phone_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin activity log (login, farmer warning/suspend, etc.)
CREATE TABLE activity_log_entry (
  id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  timestamp   DATETIME NOT NULL,
  user_phone  VARCHAR(255) NOT NULL,
  action      VARCHAR(80) NOT NULL,
  details     TEXT NULL,
  ip_address  VARCHAR(64) NULL,
  INDEX ix_activity_log_entry_user_phone (user_phone),
  INDEX ix_activity_log_entry_action (action),
  INDEX ix_activity_log_entry_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE account_settings (
  account_settings_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id             BIGINT UNSIGNED NOT NULL UNIQUE,
  notify_push         TINYINT(1) NOT NULL DEFAULT 1,
  language_code       VARCHAR(10) NOT NULL DEFAULT 'en',
  privacy_level       ENUM('public','private') NOT NULL DEFAULT 'private',
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_account_settings_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE farm_information (
  farm_info_id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id               BIGINT UNSIGNED NOT NULL UNIQUE,
  farm_name               VARCHAR(150) NULL,
  ownership_status        VARCHAR(40) NULL COMMENT 'Register-farm wizard: landowner | cloa_holder | list_holder | sessional_farm_worker | others',
  farm_address            VARCHAR(255) NULL,
  region                  VARCHAR(100) NULL,
  province                VARCHAR(100) NULL,
  municipality            VARCHAR(100) NULL,
  barangay                VARCHAR(150) NULL,
  farm_size_ha            DECIMAL(10,2) NULL,
  elevation_masl          DECIMAL(10,2) NULL,
  latitude                DECIMAL(10,7) NULL,
  longitude               DECIMAL(10,7) NULL,
  created_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_farm_info_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tree_counts (
  tree_count_id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                   BIGINT UNSIGNED NOT NULL,
  record_year                 YEAR NOT NULL,
  robusta_bearing             INT UNSIGNED NOT NULL DEFAULT 0,
  robusta_non_bearing         INT UNSIGNED NOT NULL DEFAULT 0,
  liberica_bearing            INT UNSIGNED NOT NULL DEFAULT 0,
  liberica_non_bearing        INT UNSIGNED NOT NULL DEFAULT 0,
  excelsa_bearing             INT UNSIGNED NOT NULL DEFAULT 0,
  excelsa_non_bearing         INT UNSIGNED NOT NULL DEFAULT 0,
  total_tree_count            INT UNSIGNED GENERATED ALWAYS AS (
    robusta_bearing + robusta_non_bearing +
    liberica_bearing + liberica_non_bearing +
    excelsa_bearing + excelsa_non_bearing
  ) STORED,
  created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_tree_farmer_year (farmer_id, record_year),
  CONSTRAINT fk_tree_counts_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE affiliation_information (
  affiliation_info_id      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                BIGINT UNSIGNED NOT NULL UNIQUE,
  federation_assoc         VARCHAR(150) NULL,
  coop_name                VARCHAR(150) NULL,
  ncfrs                    TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'NCFRS: 1=yes 0=no',
  rsbsa_registered         TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=no 1=yes 2=pending (legacy)',
  rsbsa_number             VARCHAR(100) NULL COMMENT 'RSBSA ID when registered; N/A when rsbsa_registered=0',
  rsbsa_status             VARCHAR(40) NULL COMMENT 'When not registered: not_yet_applied | pending_rsbsa',
  notes                    TEXT NULL,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_affiliation_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE production_information (
  production_info_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                BIGINT UNSIGNED NOT NULL,
  production_year          YEAR NOT NULL,
  robusta_qty_kg           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  liberica_qty_kg          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  excelsa_qty_kg           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  beans_remaining_kg       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  total_qty_kg             DECIMAL(12,2) GENERATED ALWAYS AS (
    robusta_qty_kg + liberica_qty_kg + excelsa_qty_kg
  ) STORED,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_production_farmer_year (farmer_id, production_year),
  CONSTRAINT fk_production_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Client Web transaction submit (see xampp-migrate-client-transaction.sql — merged here)
CREATE TABLE customer_transaction (
  customer_transaction_id  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                BIGINT UNSIGNED NOT NULL,
  client_id                BIGINT UNSIGNED NULL,
  buyer_name               VARCHAR(150) NULL,
  product                  VARCHAR(100) NOT NULL,
  quantity                 DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  quantity_unit            VARCHAR(20) NOT NULL DEFAULT 'KG',
  amount                   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  payment_amount           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  payment_method           VARCHAR(50) NULL,
  reference_no             VARCHAR(100) NULL,
  transaction_type         VARCHAR(40) NOT NULL DEFAULT 'pickup',
  transaction_date         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  pickup_date              DATE NULL,
  pickup_date_display      VARCHAR(32) NULL,
  valid_id_path            VARCHAR(500) NULL,
  valid_id_filename        VARCHAR(255) NULL,
  submitted_from           VARCHAR(40) NOT NULL DEFAULT 'client_web',
  client_form_json         TEXT NULL,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_customer_tx_reference (reference_no),
  INDEX idx_customer_tx_farmer (farmer_id),
  INDEX idx_customer_tx_buyer (buyer_name),
  CONSTRAINT fk_customer_tx_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_customer_tx_client
    FOREIGN KEY (client_id) REFERENCES client(client_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transaction_history (
  transaction_history_id   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_transaction_id  BIGINT UNSIGNED NOT NULL,
  status                   VARCHAR(40) NOT NULL DEFAULT 'created',
  remarks                  VARCHAR(255) NULL,
  changed_by_user_id       BIGINT UNSIGNED NULL,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tx_history_tx (customer_transaction_id),
  INDEX idx_tx_history_status (status),
  CONSTRAINT fk_tx_history_tx
    FOREIGN KEY (customer_transaction_id) REFERENCES customer_transaction(customer_transaction_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_tx_history_user
    FOREIGN KEY (changed_by_user_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Client Web reports → Admin Client Report (see xampp-migrate-client-report.sql — merged here)
CREATE TABLE client_misconduct_report (
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
  INDEX idx_cmr_farmer (farmer_id),
  CONSTRAINT fk_cmr_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE shared_messages (
  message_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_role ENUM('admin','farmer') NOT NULL,
  sender_phone VARCHAR(32) NOT NULL,
  sender_name VARCHAR(255) NULL,
  recipient_role ENUM('admin','farmer') NOT NULL,
  recipient_phone VARCHAR(32) NOT NULL DEFAULT '',
  recipient_name VARCHAR(255) NULL,
  subject VARCHAR(300) NOT NULL,
  body TEXT NOT NULL,
  category VARCHAR(30) NOT NULL DEFAULT 'general',
  farmer_id BIGINT UNSIGNED NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  is_starred TINYINT(1) NOT NULL DEFAULT 0,
  is_archived TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  read_at DATETIME NULL,
  INDEX idx_sm_recipient (recipient_role, recipient_phone, is_read, is_archived),
  INDEX idx_sm_sender (sender_role, sender_phone),
  INDEX idx_sm_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE farmer_message (
  farmer_message_id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                BIGINT UNSIGNED NOT NULL,
  sender_user_id           BIGINT UNSIGNED NOT NULL,
  receiver_user_id         BIGINT UNSIGNED NOT NULL,
  message_text             TEXT NOT NULL,
  is_read                  TINYINT(1) NOT NULL DEFAULT 0,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_farmer_msg_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_farmer_msg_sender
    FOREIGN KEY (sender_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_farmer_msg_receiver
    FOREIGN KEY (receiver_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Per-action moderation history (warning / suspend / unsuspend)
CREATE TABLE farmer_moderation_logs (
  log_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      BIGINT UNSIGNED NOT NULL,
  farmer_id    BIGINT UNSIGNED NOT NULL,
  type         ENUM('warning','suspend','unsuspend','clear_warnings') NOT NULL,
  reason       VARCHAR(500) NULL,
  expires_at   DATETIME NULL COMMENT 'Para sa suspension expiration',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX fk_moderation_farmer (farmer_id),
  CONSTRAINT fk_moderation_logs_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_moderation_logs_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE farmer_notification (
  farmer_notification_id   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                BIGINT UNSIGNED NOT NULL,
  user_id                  BIGINT UNSIGNED NOT NULL,
  notification_type        VARCHAR(60) NULL,
  message                  VARCHAR(255) NOT NULL,
  is_read                  TINYINT(1) NOT NULL DEFAULT 0,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_farmer_notif_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_farmer_notif_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- IPOPHL document upload / AI analysis
CREATE TABLE document_analysis (
  id                   INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  file_uuid            VARCHAR(36) NOT NULL,
  original_filename    VARCHAR(255) NOT NULL,
  file_path            VARCHAR(500) NOT NULL,
  file_type            VARCHAR(50) NOT NULL,
  file_size            INT NOT NULL,
  ai_score             INT NULL,
  ai_status            VARCHAR(20) NULL,
  detected_features    TEXT NULL,
  missing_requirements TEXT NULL,
  analysis_method      VARCHAR(50) NULL,
  text_length          INT NULL,
  shap_analysis        TEXT NULL,
  upload_timestamp     DATETIME NOT NULL,
  analysis_timestamp   DATETIME NULL,
  ipophl_phase         VARCHAR(50) NULL,
  task_id              VARCHAR(100) NULL,
  UNIQUE KEY ix_document_analysis_file_uuid (file_uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gi_farmers_contribution (
  gi_farmer_contribution_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                 BIGINT UNSIGNED NOT NULL,
  ipophi_id                 VARCHAR(100) NULL,
  gi_document               VARCHAR(255) NULL,
  images                    TEXT NULL,
  upload_status             ENUM('pending','reviewed','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_gi_contrib_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE gi_updates (
  gi_update_id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                BIGINT UNSIGNED NULL,
  title                    VARCHAR(150) NOT NULL,
  content                  TEXT NOT NULL,
  image_url                VARCHAR(255) NULL,
  attachments_json         TEXT NULL COMMENT 'JSON array of {name,path,mime,size}',
  upload_status            ENUM('pending','approved','archived','rejected') NOT NULL DEFAULT 'pending',
  is_starred               TINYINT(1) NOT NULL DEFAULT 0,
  is_read_admin            TINYINT(1) NOT NULL DEFAULT 0,
  category                 VARCHAR(30) NOT NULL DEFAULT 'general',
  sender_name              VARCHAR(255) NULL,
  progress_percent         DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  current_phase            VARCHAR(100) NULL COMMENT 'farmer_submission | admin_progress',
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_gi_updates_farmer (farmer_id),
  INDEX idx_gi_updates_status (upload_status, is_read_admin),
  CONSTRAINT fk_gi_updates_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE social (
  social_id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  account_user_id          BIGINT UNSIGNED NOT NULL,
  platform                 VARCHAR(50) NOT NULL,
  url                      VARCHAR(255) NOT NULL,
  is_active                TINYINT(1) NOT NULL DEFAULT 1,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_social_user
    FOREIGN KEY (account_user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_farmer_notification_read ON farmer_notification(user_id, is_read);
CREATE INDEX idx_farmer_message_read ON farmer_message(receiver_user_id, is_read);
CREATE INDEX idx_customer_transaction_date ON customer_transaction(transaction_date);
CREATE INDEX idx_gi_updates_created ON gi_updates(created_at);

-- Reverse references (optional denormalized FKs)
-- These make the relationships visible on the `farmers` table itself.
-- They do not automatically populate; app code can later fill them if desired.
ALTER TABLE farmers
  ADD CONSTRAINT fk_farmers_personal_info
    FOREIGN KEY (personal_info_id) REFERENCES personal_information(personal_info_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_farmers_affiliation_info
    FOREIGN KEY (affiliation_info_id) REFERENCES affiliation_information(affiliation_info_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_farmers_farm_info
    FOREIGN KEY (farm_info_id) REFERENCES farm_information(farm_info_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_farmers_latest_tree_count
    FOREIGN KEY (latest_tree_count_id) REFERENCES tree_counts(tree_count_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_farmers_latest_production_info
    FOREIGN KEY (latest_production_info_id) REFERENCES production_information(production_info_id)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- -----------------------------------------------------------------------------
-- FULL IMPORT (phpMyAdmin) — bagong / wipe database. Isang file lang; huwag na mag-run
-- ng hiwalay na migrate kung fresh import ito. Walang sample INSERT data.
--
-- Schema aligned with official dump: beanthentic_app.sql (May 26, 2026), including:
--   admin_user, activity_log_entry, document_analysis, farmer_moderation_logs
--
-- Na-merge na dito ang:
--   xampp-migrate-client-transaction.sql
--     → customer_transaction: transaction_type, pickup_date, pickup_date_display,
--       valid_id_path, valid_id_filename, quantity_unit, submitted_from, client_form_json
--   xampp-migrate-client-report.sql
--     → client_misconduct_report (Client Web report → Admin Client Report)
--   xampp-beanthentic-migrate-existing.sql (schema sa CREATE TABLE, hindi ALTER)
--     → farm_information.ownership_status VARCHAR(40)
--     → affiliation_information: ncfrs, rsbsa_registered, rsbsa_number, rsbsa_status
--   xampp-beanthentic-alter-users.sql (schema sa users CREATE, hindi ALTER)
--     → phone_number E.164, username display name, email nullable
--
-- HINDI kasama (hiwalay pa rin — XAMPP server / MySQL user, hindi table schema):
--   xampp-enable-lan-mysql.sql — LAN user + bind-address para sa admin/client web
--
-- Lumang database na MAY DATA na (ayaw mag-DROP tables):
--   1) xampp-beanthentic-migrate-existing.sql
--   2) xampp-migrate-client-transaction.sql (kung kulang ang columns)
--   3) xampp-migrate-client-report.sql (kung wala pa ang table)
-- -----------------------------------------------------------------------------
