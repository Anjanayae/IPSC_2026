-- Database schema for IPSC 2026 Registration System
-- This file creates the necessary tables for storing registration data

-- Create database (if needed)

-- Drop table if exists (for clean installation)
DROP TABLE IF EXISTS registrations;

-- Create registrations table
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prefix VARCHAR(10) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    affiliation TEXT NOT NULL,
    country VARCHAR(100) NOT NULL,
    submitted_paper VARCHAR(5) NOT NULL,
    abstract_id VARCHAR(100) DEFAULT NULL,
    registration_type VARCHAR(255) NOT NULL,
    payment_reference VARCHAR(255) DEFAULT NULL,
    amount_transferred DECIMAL(10, 2) DEFAULT 0.00,
    remarks TEXT DEFAULT NULL,
    payment_status VARCHAR(50) NOT NULL,
    approval_status VARCHAR(20) DEFAULT 'pending',
    rejection_reason TEXT DEFAULT NULL,
    approved_by INT DEFAULT NULL,
    approved_at TIMESTAMP NULL DEFAULT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_registration_date (registration_date),
    INDEX idx_payment_status (payment_status),
    INDEX idx_approval_status (approval_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;