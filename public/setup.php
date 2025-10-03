<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Food Ordering System Setup</h2>";

$host = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected to MySQL successfully<br>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS food_ordering";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("food_ordering");

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        type ENUM('customer', 'vendor') DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS food_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        category VARCHAR(50) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_color VARCHAR(7),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100),
        customer_email VARCHAR(100),
        customer_phone VARCHAR(20),
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'confirmed', 'preparing', 'delivered') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        food_item_id INT,
        food_name VARCHAR(100),
        quantity INT,
        price DECIMAL(10,2),
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Clear existing food items to avoid duplicates
$conn->query("DELETE FROM food_items");

// Insert new food data (without duplicates)
$sample_foods = [
    "('Biryani', 'rice', 'Fragrant rice cooked with aromatic spices and tender meat', 320, '#8B4513')",
    "('Hilsa Fish Curry', 'curry', 'National fish cooked in mustard gravy with spices', 450, '#FFD700')",
    "('Beef Bhuna', 'curry', 'Slow-cooked beef with traditional Bengali spices', 280, '#8B0000')",
    "('Bhuna Khichuri', 'rice', 'Aromatic rice and lentils cooked with special spices', 180, '#CD853F')",
    "('Kala Bhuna', 'curry', 'Traditional dark beef curry with rich flavors', 350, '#654321')"
];

$insert_sql = "INSERT INTO food_items (name, category, description, price, image_color) VALUES " . implode(",", $sample_foods);
if ($conn->query($insert_sql) === TRUE) {
    echo "Food data inserted successfully<br>";
    echo "Added: Biryani, Hilsa Fish Curry, Beef Bhuna, Bhuna Khichuri, Kala Bhuna<br>";
} else {
    echo "Error inserting data: " . $conn->error . "<br>";
}

$conn->close();
echo "<h3 style='color: green;'>Database setup completed successfully!</h3>";
echo "<p><a href='index.html'>Go to Customer Site</a> | <a href='restaurant/index.html'>Go to Restaurant Portal</a></p>";
