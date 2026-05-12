-- Run once on an existing XAMPP `beanthentic_app` DB to align `users` with login/signup APIs.
USE beanthentic_app;

ALTER TABLE users
  MODIFY COLUMN phone_number VARCHAR(32) NOT NULL COMMENT 'E.164 e.g. +639XXXXXXXXX',
  MODIFY COLUMN username VARCHAR(150) NULL COMMENT 'Display / full name',
  MODIFY COLUMN email VARCHAR(191) NULL DEFAULT NULL;
