<?php
require_once 'classes/DatabaseConnection.php';
require_once 'classes/DatabaseQuery.php';

$connection = new DatabaseConnection();
$query = new DatabaseQuery($connection);

// Create tables
$query->executeQuery("
    CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        capacity INT DEFAULT 1,
        is_available BOOLEAN DEFAULT TRUE
    )
");

$query->executeQuery("
    CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_id INT,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        booking_time DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('booked', 'in_queue', 'cancelled', 'completed') DEFAULT 'booked',
        expiry_time DATETIME,
        FOREIGN KEY (room_id) REFERENCES rooms(id)
    )
");

$query->executeQuery("
    CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )
");

// Insert sample data
$query->executeQuery("INSERT IGNORE INTO rooms (name) VALUES ('Room 1'), ('Room 2'), ('Room 3')");

// Create default admin if not exists
$adminExists = $query->fetchOne("SELECT id FROM admin WHERE username = 'admin' LIMIT 1");
if (!$adminExists) {
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $query->executeQuery("INSERT INTO admin (username, password) VALUES (?, ?)", ['admin', $hashedPassword], 'ss');
}

echo "Database initialized successfully!";
?>