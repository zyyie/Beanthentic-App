-- =============================================================================
-- Beanthentic — migration para sa LUMANG database (may data na)
-- Huwag itong i-import kasabay ng full xampp-beanthentic-import.sql.
-- Patakbuhin lang ang mga statement na kailangan mo; i-skip ang may error.
-- =============================================================================

USE beanthentic_app;

-- Kung dati pa ENUM ang ownership_status:
ALTER TABLE farm_information
  MODIFY ownership_status VARCHAR(40) NULL
  COMMENT 'Register-farm wizard: landowner | cloa_holder | list_holder | sessional_farm_worker | others';

-- Kung WALANG column na ncfrs (error "Duplicate column" = mayroon na, OK lang i-skip):
ALTER TABLE affiliation_information
  ADD COLUMN ncfrs TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'NCFRS: 1=yes 0=no' AFTER coop_name;

-- RSBSA Registered (0=no, 1=yes, 2=pending legacy):
ALTER TABLE affiliation_information
  MODIFY rsbsa_registered TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=no 1=yes 2=pending (legacy)';

-- RSBSA Registered Number (N/A kapag hindi registered):
ALTER TABLE affiliation_information
  MODIFY rsbsa_number VARCHAR(100) NULL COMMENT 'RSBSA ID when registered; N/A when rsbsa_registered=0';

-- RSBSA Status — bagong column para sa farmer registration (import sa admin PHP):
-- Values: not_yet_applied = "Not Yet Applied", pending_rsbsa = "Pending RSBSA"
-- Kung "Duplicate column name" = mayroon na, i-skip lang ang statement na ito.
ALTER TABLE affiliation_information
  ADD COLUMN rsbsa_status VARCHAR(40) NULL COMMENT 'not_yet_applied | pending_rsbsa' AFTER rsbsa_number;

-- Farmer account moderation (admin Warning / Suspend)
-- Kung "Duplicate column name" = mayroon na, i-skip lang ang statement na iyon.
ALTER TABLE farmers
  ADD COLUMN is_suspended TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = suspended' AFTER updated_at;
ALTER TABLE farmers
  ADD COLUMN suspended_until DATETIME NULL COMMENT 'Auto-unsuspend when passed' AFTER is_suspended;
ALTER TABLE farmers
  ADD COLUMN suspension_reason VARCHAR(500) NULL AFTER suspended_until;
ALTER TABLE farmers
  ADD COLUMN warning_count INT NOT NULL DEFAULT 0 AFTER suspension_reason;
ALTER TABLE farmers
  ADD COLUMN last_warning_at DATETIME NULL AFTER warning_count;
ALTER TABLE farmers
  ADD COLUMN last_warning_reason VARCHAR(500) NULL AFTER last_warning_at;

-- GI Updates / Farmer contributions (app ↔ admin IPOPHL inbox)
ALTER TABLE gi_updates
  ADD COLUMN attachments_json TEXT NULL COMMENT 'JSON array of {name,path,mime,size}' AFTER image_url;
ALTER TABLE gi_updates
  ADD COLUMN upload_status ENUM('pending','approved','archived','rejected') NOT NULL DEFAULT 'pending' AFTER attachments_json;
ALTER TABLE gi_updates
  ADD COLUMN is_starred TINYINT(1) NOT NULL DEFAULT 0 AFTER upload_status;
ALTER TABLE gi_updates
  ADD COLUMN is_read_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER is_starred;
ALTER TABLE gi_updates
  ADD COLUMN category VARCHAR(30) NOT NULL DEFAULT 'general' AFTER is_read_admin;
ALTER TABLE gi_updates
  ADD COLUMN sender_name VARCHAR(255) NULL AFTER category;
