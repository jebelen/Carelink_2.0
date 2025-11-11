-- Create the database
CREATE DATABASE IF NOT EXISTS carelink_db;

-- Use the created database
USE carelink_db;

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('barangay_staff', 'department_admin') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    barangay VARCHAR(100) DEFAULT NULL,
    display_name VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (username),
    UNIQUE KEY (email)
);

-- Create the settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    theme VARCHAR(50) NOT NULL DEFAULT 'light',
    language VARCHAR(50) NOT NULL DEFAULT 'en',
    notifications VARCHAR(50) NOT NULL DEFAULT 'all',
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create the notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT(11) NOT NULL AUTO_INCREMENT,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)

);

-- Create the remember_tokens table
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    selector VARCHAR(12) NOT NULL UNIQUE,
    validator_hash VARCHAR(64) NOT NULL,
    expires DATETIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
    
-- Create the applications table
CREATE TABLE IF NOT EXISTS applications (
    id INT(11) NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    application_type VARCHAR(255) NOT NULL,
    birth_date DATE NOT NULL,
    contact_number VARCHAR(255) NOT NULL,
    complete_address TEXT NOT NULL,
    emergency_contact VARCHAR(255) NOT NULL,
    emergency_contact_name VARCHAR(255) NOT NULL,
    barangay VARCHAR(255) NOT NULL,
    proof_of_address BLOB DEFAULT NULL,
    proof_of_address_type VARCHAR(255) DEFAULT NULL,
    id_image BLOB DEFAULT NULL,
    id_image_type VARCHAR(255) DEFAULT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lastName VARCHAR(255) DEFAULT NULL,
    firstName VARCHAR(255) DEFAULT NULL,
    middleName VARCHAR(255) DEFAULT NULL,
    suffix VARCHAR(255) DEFAULT NULL,
    disabilityType TEXT DEFAULT NULL,
    id_number VARCHAR(255) DEFAULT NULL,
    pwd_id_issue_date DATE DEFAULT NULL,
    pwd_id_expiry_date DATE DEFAULT NULL,
    PRIMARY KEY (id)
);
