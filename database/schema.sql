-- FSSAI License Verification & Reporting System
-- Database Schema for MySQL

-- Drop tables if they exist (for clean reinstall)
DROP TABLE IF EXISTS verification_logs;
DROP TABLE IF EXISTS reported_fake_licenses;
DROP TABLE IF EXISTS valid_licenses;
DROP TABLE IF EXISTS admin_users;

-- ============================================
-- Table: valid_licenses
-- Stores authentic FSSAI licenses for verification
-- ============================================
CREATE TABLE valid_licenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_number VARCHAR(14) UNIQUE NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    owner_name VARCHAR(100),
    address TEXT NOT NULL,
    city VARCHAR(100),
    state VARCHAR(50),
    pincode VARCHAR(6),
    license_type ENUM('Basic', 'State', 'Central') DEFAULT 'Basic',
    issue_date DATE,
    expiry_date DATE,
    status ENUM('Active', 'Suspended', 'Cancelled', 'Expired') DEFAULT 'Active',
    food_category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_license_number (license_number),
    INDEX idx_status (status),
    INDEX idx_expiry (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: reported_fake_licenses
-- Stores user reports of suspicious/fake licenses
-- ============================================
CREATE TABLE reported_fake_licenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_number VARCHAR(14) NOT NULL,
    reporter_name VARCHAR(100),
    reporter_email VARCHAR(100),
    reporter_phone VARCHAR(15),
    location VARCHAR(255) NOT NULL,
    city VARCHAR(100),
    state VARCHAR(50),
    address TEXT,
    description TEXT,
    evidence_image VARCHAR(255),
    evidence_image_path TEXT,
    status ENUM('Pending', 'Under Review', 'Verified Fake', 'Dismissed') DEFAULT 'Pending',
    admin_notes TEXT,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by VARCHAR(100),
    INDEX idx_license_number (license_number),
    INDEX idx_status (status),
    INDEX idx_reported_at (reported_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: verification_logs
-- Tracks all verification attempts for analytics
-- ============================================
CREATE TABLE verification_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_number VARCHAR(14) NOT NULL,
    input_method ENUM('Manual', 'QR/Image') DEFAULT 'Manual',
    result ENUM('Valid', 'Invalid', 'Expired') DEFAULT 'Invalid',
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_license_number (license_number),
    INDEX idx_result (result),
    INDEX idx_verified_at (verified_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: admin_users
-- Simple admin authentication
-- ============================================
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data: Admin User
-- Default credentials: admin / Admin123!
-- ============================================
INSERT INTO admin_users (username, password_hash, email) VALUES
('admin', '$2y$12$IeczVSlpdzpmZuI91He.Y.lxs43KdwOAy1Y4DOinJe6Xg8qeGMr1W', 'admin@fssai-verify.gov.in');

-- ============================================
-- Sample Data: Valid Licenses
-- 15 sample licenses across different Indian states
-- ============================================
INSERT INTO valid_licenses (license_number, business_name, owner_name, address, city, state, pincode, license_type, issue_date, expiry_date, status, food_category) VALUES
-- Maharashtra
('10012021000001', 'Mumbai Fresh Foods Pvt Ltd', 'Rajesh Kumar', 'Plot No. 45, MIDC Industrial Area, Andheri East', 'Mumbai', 'Maharashtra', '400093', 'Central', '2021-01-15', '2026-01-14', 'Active', 'Manufacturing/Processing'),
('10012022000002', 'Pune Dairy Products', 'Priya Sharma', 'Survey No. 123, Hadapsar Industrial Estate', 'Pune', 'Maharashtra', '411028', 'State', '2022-03-20', '2027-03-19', 'Active', 'Dairy'),
('10012021000003', 'Nagpur Snacks Industries', 'Amit Deshmukh', 'Kalmeshwar Road, Butibori', 'Nagpur', 'Maharashtra', '440022', 'Basic', '2021-06-10', '2026-06-09', 'Active', 'Snacks'),

-- Delhi
('11012020000004', 'Delhi Organic Foods', 'Neha Gupta', 'A-45, Okhla Industrial Area Phase-II', 'New Delhi', 'Delhi', '110020', 'Central', '2020-09-01', '2025-08-31', 'Active', 'Organic Foods'),
('11012022000005', 'Capital Beverages Ltd', 'Vikram Singh', 'Plot 78, Wazirpur Industrial Area', 'New Delhi', 'Delhi', '110052', 'State', '2022-11-15', '2027-11-14', 'Active', 'Beverages'),

-- Karnataka
('12012021000006', 'Bangalore Tech Canteen', 'Suresh Reddy', 'Electronic City Phase I, Hosur Road', 'Bangalore', 'Karnataka', '560100', 'Basic', '2021-04-22', '2026-04-21', 'Active', 'Food Service'),
('12012020000007', 'Mysore Spice Mills', 'Lakshmi Narayan', 'Kuvempunagar, Hunsur Road', 'Mysore', 'Karnataka', '570023', 'State', '2020-07-30', '2025-07-29', 'Active', 'Spices'),
('12012022000008', 'Coastal Seafood Exports', 'Anthony D Souza', 'NH-66, Udupi-Mangalore Highway', 'Udupi', 'Karnataka', '576104', 'Central', '2022-02-14', '2027-02-13', 'Active', 'Seafood'),

-- Tamil Nadu
('13012021000009', 'Chennai Ready Meals', 'Subramanian Iyer', 'Guindy Industrial Estate, Mount Road', 'Chennai', 'Tamil Nadu', '600032', 'State', '2021-08-05', '2026-08-04', 'Active', 'Ready-to-Eat'),
('13012022000010', 'Coimbatore Textiles Canteen', 'Palanisamy Gounder', 'SIDCO Industrial Estate, Kurichi', 'Coimbatore', 'Tamil Nadu', '641021', 'Basic', '2022-05-18', '2027-05-17', 'Active', 'Food Service'),

-- Gujarat
('14012020000011', 'Ahmedabad Food Corporation', 'Kiran Patel', 'Sarkhej-Bavla Highway, Changodar', 'Ahmedabad', 'Gujarat', '382213', 'Central', '2020-12-01', '2025-11-30', 'Active', 'Grain Processing'),
('14012021000012', 'Surat Diamond Cafeteria', 'Ramesh Shah', 'Katargam GIDC, Sachin', 'Surat', 'Gujarat', '394230', 'Basic', '2021-10-25', '2026-10-24', 'Active', 'Food Service'),

-- Uttar Pradesh
('15012022000013', 'Lucknow Nawabi Foods', 'Mohammad Asif', 'Amausi Industrial Area', 'Lucknow', 'Uttar Pradesh', '226009', 'State', '2022-01-10', '2027-01-09', 'Active', 'Meat Products'),
('15012021000014', 'Kanpur Leather Factory Canteen', 'Ram Prakash', 'Panki Industrial Area', 'Kanpur', 'Uttar Pradesh', '208019', 'Basic', '2021-03-28', '2026-03-27', 'Active', 'Food Service'),

-- West Bengal
('16012020000015', 'Kolkata Sweets & Confectionery', 'Dhirendra Nath Banerjee', 'Salt Lake Sector V, IT Park', 'Kolkata', 'West Bengal', '700091', 'State', '2020-11-20', '2025-11-19', 'Active', 'Sweets/Confectionery');

-- ============================================
-- Views for Admin Dashboard
-- ============================================
CREATE OR REPLACE VIEW v_pending_reports AS
SELECT
    r.id,
    r.license_number,
    r.reporter_name,
    r.reporter_email,
    r.location,
    r.city,
    r.state,
    r.description,
    r.evidence_image,
    r.reported_at,
    COUNT(v.id) as verification_count
FROM reported_fake_licenses r
LEFT JOIN verification_logs v ON r.license_number = v.license_number
WHERE r.status = 'Pending'
GROUP BY r.id
ORDER BY r.reported_at DESC;

CREATE OR REPLACE VIEW v_license_statistics AS
SELECT
    status,
    COUNT(*) as count
FROM valid_licenses
GROUP BY status
UNION ALL
SELECT
    'Total Reports' as status,
    COUNT(*) as count
FROM reported_fake_licenses
WHERE status IN ('Pending', 'Under Review');
