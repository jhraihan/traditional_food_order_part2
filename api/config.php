<?php
// Enable error reporting but don't display errors in production
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Vercel environment variables
$host = getenv('MYSQL_HOST') ?: "localhost";
$username = getenv('MYSQL_USERNAME') ?: "root";
$password = getenv('MYSQL_PASSWORD') ?: "";
$database = getenv('MYSQL_DATABASE') ?: "food_ordering";

// For Vercel deployment, we'll use SQLite as MySQL isn't available
if (getenv('VERCEL')) {
    // Use SQLite for Vercel deployment
    class VercelDB
    {
        private $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('sqlite:' . __DIR__ . '/food_ordering.db');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTables();
        }

        private function createTables()
        {
            // Create food_items table
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS food_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL,
                description TEXT,
                price REAL NOT NULL,
                image_color TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            // Create orders table
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_name TEXT,
                customer_email TEXT,
                customer_phone TEXT,
                total_amount REAL NOT NULL,
                status TEXT DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            // Create order_items table
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER,
                food_item_id INTEGER,
                food_name TEXT,
                quantity INTEGER,
                price REAL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            )");

            // Insert sample data if empty
            $this->insertSampleData();
        }

        private function insertSampleData()
        {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM food_items");
            if ($stmt->fetchColumn() == 0) {
                $sampleFoods = [
                    ['Biryani', 'rice', 'Fragrant rice cooked with aromatic spices and tender meat', 320, '#8B4513'],
                    ['Hilsa Fish Curry', 'curry', 'National fish cooked in mustard gravy with spices', 450, '#FFD700'],
                    ['Beef Bhuna', 'curry', 'Slow-cooked beef with traditional Bengali spices', 280, '#8B0000'],
                    ['Bhuna Khichuri', 'rice', 'Aromatic rice and lentils cooked with special spices', 180, '#CD853F'],
                    ['Kala Bhuna', 'curry', 'Traditional dark beef curry with rich flavors', 350, '#654321']
                ];

                $stmt = $this->pdo->prepare("INSERT INTO food_items (name, category, description, price, image_color) VALUES (?, ?, ?, ?, ?)");
                foreach ($sampleFoods as $food) {
                    $stmt->execute($food);
                }
            }
        }

        public function getConnection()
        {
            return $this->pdo;
        }
    }

    $vercelDB = new VercelDB();
    $conn = $vercelDB->getConnection();
} else {
    // Use MySQL for local development
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }
}

// Start session for PHP authentication
session_start();

// Simple authentication check function
function checkAuth($requiredRole = null)
{
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }

    if ($requiredRole && $_SESSION['user']['role'] !== $requiredRole) {
        http_response_code(403);
        echo json_encode(["error" => "Forbidden"]);
        exit;
    }

    return true;
}
