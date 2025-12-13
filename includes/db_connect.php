<?php
i // Database connection details will be read from environment variables on Render
$dbUrl = getenv('DATABASE_URL');

if ($dbUrl === false) {
    // Fallback for local development if .env files are not used
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "carelink_db";
    $conn_str = "mysql:host=$servername;dbname=$dbname";
    $pdo_username = $username;
    $pdo_password = $password;
} else {
    // Parse the DATABASE_URL from Render
    $dbopts = parse_url($dbUrl);
    $servername = $dbopts["host"];
    $dbname = ltrim($dbopts["path"], '/');
    $pdo_username = $dbopts["user"];
    $pdo_password = $dbopts["pass"];
    $port = $dbopts["port"];
    // The driver is pgsql for PostgreSQL on Render
    $conn_str = "pgsql:host=$servername;port=$port;dbname=$dbname";
}


try {
    $conn = new PDO($conn_str, $pdo_username, $pdo_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create system_settings table if it doesn't exist and insert default values
    // Note: AUTO_INCREMENT is `SERIAL` in PostgreSQL
    $conn->exec("
        CREATE TABLE IF NOT EXISTS system_settings (
            id INT PRIMARY KEY DEFAULT 1,
            session_timeout INT NOT NULL DEFAULT 30,
            max_login_attempts INT NOT NULL DEFAULT 5,
            backup_frequency VARCHAR(50) NOT NULL DEFAULT 'weekly',
            auto_backup BOOLEAN NOT NULL DEFAULT TRUE
        );
    ");
    
    // Use ON CONFLICT DO NOTHING for PostgreSQL to avoid inserting duplicates
    $conn->exec("
        INSERT INTO system_settings (id, session_timeout, max_login_attempts, backup_frequency, auto_backup)
        VALUES (1, 30, 5, 'weekly', TRUE)
        ON CONFLICT (id) DO NOTHING;
    ");


} catch(PDOException $e) {
    // Instead of echoing, re-throw the exception or handle it silently for an API.
    // The calling script will catch this PDOException and return a JSON error.
    throw $e;
}
?>