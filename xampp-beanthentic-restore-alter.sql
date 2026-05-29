-- =============================================================================

-- Beanthentic — ayusin ang lumang DB papunta sa official schema (ALTER lang)

-- Official base: xampp-beanthentic-import.sql (phpMyAdmin dump May 26, 2026)

--

-- Gamit: phpMyAdmin → database beanthentic_app → SQL tab → paste/run

-- Kung "Duplicate column" / "Unknown column" = i-skip lang ang statement na iyon.

-- =============================================================================



USE beanthentic_app;



-- farmers — moderation columns (pareho sa official import)

ALTER TABLE farmers

  ADD COLUMN is_suspended TINYINT(1) NOT NULL DEFAULT 0 AFTER latest_production_info_id;

ALTER TABLE farmers

  ADD COLUMN suspended_until DATETIME NULL AFTER is_suspended;

ALTER TABLE farmers

  ADD COLUMN suspension_reason VARCHAR(500) NULL AFTER suspended_until;

ALTER TABLE farmers

  ADD COLUMN warning_count INT NOT NULL DEFAULT 0 AFTER suspension_reason;

ALTER TABLE farmers

  ADD COLUMN last_warning_at DATETIME NULL AFTER warning_count;

ALTER TABLE farmers

  ADD COLUMN last_warning_reason VARCHAR(500) NULL AFTER last_warning_at;



ALTER TABLE farmers

  MODIFY COLUMN is_suspended TINYINT(1) NOT NULL DEFAULT 0 AFTER latest_production_info_id,

  MODIFY COLUMN suspended_until DATETIME NULL AFTER is_suspended,

  MODIFY COLUMN suspension_reason VARCHAR(500) NULL AFTER suspended_until,

  MODIFY COLUMN warning_count INT NOT NULL DEFAULT 0 AFTER suspension_reason,

  MODIFY COLUMN last_warning_at DATETIME NULL AFTER warning_count,

  MODIFY COLUMN last_warning_reason VARCHAR(500) NULL AFTER last_warning_at;



-- affiliation / farm (mula sa official dump)

ALTER TABLE farm_information

  MODIFY ownership_status VARCHAR(40) NULL

  COMMENT 'Register-farm wizard: landowner | cloa_holder | list_holder | sessional_farm_worker | others';



ALTER TABLE affiliation_information

  ADD COLUMN ncfrs TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'NCFRS: 1=yes 0=no' AFTER coop_name;



ALTER TABLE affiliation_information

  MODIFY rsbsa_registered TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=no 1=yes 2=pending (legacy)';



ALTER TABLE affiliation_information

  MODIFY rsbsa_number VARCHAR(100) NULL COMMENT 'RSBSA ID when registered; N/A when rsbsa_registered=0';



ALTER TABLE affiliation_information

  ADD COLUMN rsbsa_status VARCHAR(40) NULL COMMENT 'not_yet_applied | pending_rsbsa' AFTER rsbsa_number;



ALTER TABLE affiliation_information

  MODIFY rsbsa_status VARCHAR(40) NULL COMMENT 'When not registered: not_yet_applied | pending_rsbsa';



-- farmer_moderation_logs (official import may table na ito)

CREATE TABLE IF NOT EXISTS farmer_moderation_logs (

  log_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  user_id BIGINT UNSIGNED NOT NULL,

  farmer_id BIGINT UNSIGNED NOT NULL,

  type ENUM('warning','suspend','unsuspend','clear_warnings') NOT NULL,

  reason VARCHAR(500) NULL,

  expires_at DATETIME NULL COMMENT 'Para sa suspension expiration',

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (log_id),

  KEY fk_moderation_farmer (farmer_id),

  CONSTRAINT fk_moderation_farmer FOREIGN KEY (farmer_id) REFERENCES farmers (farmer_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


