CREATE DATABASE IF NOT EXISTS beanthentic_app
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE beanthentic_app;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS account_settings;
DROP TABLE IF EXISTS social;
DROP TABLE IF EXISTS gi_updates;
DROP TABLE IF EXISTS gi_farmers_contribution;
DROP TABLE IF EXISTS farmer_notification;
DROP TABLE IF EXISTS farmer_message;
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
  profile_photo      VARCHAR(255) NULL,
  status             ENUM('pending','active','inactive') NOT NULL DEFAULT 'pending',
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_farmers_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE personal_information (
  personal_info_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id              BIGINT UNSIGNED NOT NULL UNIQUE,
  first_name             VARCHAR(100) NULL,
  last_name              VARCHAR(100) NULL,
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
  ownership_status        ENUM('owner','tenant','co-owner','other') NULL,
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
  rsbsa_registered         TINYINT(1) NOT NULL DEFAULT 0,
  rsbsa_number             VARCHAR(100) NULL,
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

CREATE TABLE customer_transaction (
  customer_transaction_id  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id                BIGINT UNSIGNED NOT NULL,
  client_id                BIGINT UNSIGNED NULL,
  buyer_name               VARCHAR(150) NULL,
  product                  VARCHAR(100) NOT NULL,
  quantity                 DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  amount                   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  payment_amount           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  payment_method           VARCHAR(50) NULL,
  reference_no             VARCHAR(100) NULL,
  transaction_date         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  CONSTRAINT fk_tx_history_tx
    FOREIGN KEY (customer_transaction_id) REFERENCES customer_transaction(customer_transaction_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_tx_history_user
    FOREIGN KEY (changed_by_user_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

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
  progress_percent         DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  current_phase            VARCHAR(100) NULL,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_gi_updates_farmer
    FOREIGN KEY (farmer_id) REFERENCES farmers(farmer_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

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
