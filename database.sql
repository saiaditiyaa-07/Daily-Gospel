-- Daily Gospel Database Schema
-- MySQL 8.0+

CREATE DATABASE IF NOT EXISTS daily_gospel
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE daily_gospel;

-- Admin users
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') NOT NULL DEFAULT 'admin',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- User bookmarks (session-based, no readings stored)
CREATE TABLE IF NOT EXISTS bookmarks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128) NOT NULL,
    user_id INT UNSIGNED NULL,
    reading_date DATE NOT NULL,
    title VARCHAR(255) NOT NULL DEFAULT '',
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session (session_id),
    INDEX idx_reading_date (reading_date),
    INDEX idx_user (user_id),
    CONSTRAINT fk_bookmarks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Prayer requests
CREATE TABLE IF NOT EXISTS prayer_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL DEFAULT 'Anonymous',
    email VARCHAR(255) NULL,
    request_text TEXT NOT NULL,
    is_anonymous TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('pending', 'prayed', 'archived') NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- User feedback
CREATE TABLE IF NOT EXISTS feedback (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL DEFAULT 'Anonymous',
    email VARCHAR(255) NULL,
    subject VARCHAR(255) NOT NULL DEFAULT 'General Feedback',
    message TEXT NOT NULL,
    rating TINYINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('new', 'read', 'responded', 'archived') NOT NULL DEFAULT 'new',
    admin_notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Site settings (no readings stored)
CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description VARCHAR(255) NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
    ('site_name', 'Daily Gospel', 'Website display name'),
    ('site_tagline', 'Catholic Daily Mass Readings', 'Site tagline'),
    ('universalis_region', '', 'Universalis calendar region path'),
    ('calendar_id', 'default', 'Liturgical calendar identifier'),
    ('contact_email', 'admin@dailygospel.local', 'Admin contact email'),
    ('maintenance_mode', '0', 'Enable maintenance mode (0/1)'),
    ('default_language', 'ta', 'Default system language (en/ta)')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Default admin user (password set via admin/setup.php)
-- After importing, visit: /admin/setup.php?password=YourSecurePassword
-- Then DELETE admin/setup.php
