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
    email_address VARCHAR(255) DEFAULT NULL,
    complete_address TEXT NOT NULL,
    emergency_contact VARCHAR(255) NOT NULL,
    emergency_contact_name VARCHAR(255) NOT NULL,
    medical_conditions TEXT DEFAULT NULL,
    additional_notes TEXT DEFAULT NULL,
    barangay VARCHAR(255) NOT NULL,
    birth_certificate BLOB DEFAULT NULL,
    birth_certificate_type VARCHAR(255) DEFAULT NULL,
    medical_certificate BLOB DEFAULT NULL,
    medical_certificate_type VARCHAR(255) DEFAULT NULL,
    client_identification BLOB DEFAULT NULL,
    client_identification_type VARCHAR(255) DEFAULT NULL,
    proof_of_address BLOB DEFAULT NULL,
    proof_of_address_type VARCHAR(255) DEFAULT NULL,
    id_image BLOB DEFAULT NULL,
    id_image_type VARCHAR(255) DEFAULT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

ALTER TABLE `applications`
ADD COLUMN `lastName` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `firstName` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `middleName` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `suffix` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `religion` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `sex` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `civilStatus` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `bloodType` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `disabilityType` TEXT DEFAULT NULL,
ADD COLUMN `disabilityCause` TEXT DEFAULT NULL,
ADD COLUMN `educationalAttainment` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `employmentStatus` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `occupation` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `sssNo` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `gsisNo` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `pagibigNo` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `philhealthNo` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `fatherName` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `motherName` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `placeOfBirth` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `yearsInPasig` INT DEFAULT NULL,
ADD COLUMN `citizenship` VARCHAR(255) DEFAULT NULL;