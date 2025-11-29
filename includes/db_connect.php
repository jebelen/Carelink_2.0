<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carelink_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create system_settings table if it doesn't exist and insert default values
    $conn->exec("
        CREATE TABLE IF NOT EXISTS system_settings (
            id INT PRIMARY KEY DEFAULT 1,
            session_timeout INT NOT NULL DEFAULT 30,
            max_login_attempts INT NOT NULL DEFAULT 5,
            backup_frequency VARCHAR(50) NOT NULL DEFAULT 'weekly',
            auto_backup BOOLEAN NOT NULL DEFAULT TRUE
        );

        INSERT IGNORE INTO system_settings (id, session_timeout, max_login_attempts, backup_frequency, auto_backup)
        VALUES (1, 30, 5, 'weekly', TRUE);
    ");

} catch(PDOException $e) {
    // Instead of echoing, re-throw the exception or handle it silently for an API.
    // The calling script will catch this PDOException and return a JSON error.
    throw $e;
}
?>