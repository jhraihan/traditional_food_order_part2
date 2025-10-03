<?php
include 'config.php';

try {
    if (getenv('VERCEL')) {
        // SQLite version
        $stmt = $conn->query("SELECT * FROM food_items");
        $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // MySQL version
        $sql = "SELECT * FROM food_items";
        $result = $conn->query($sql);
        $foods = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $foods[] = $row;
            }
        }
    }

    echo json_encode($foods);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch food items: " . $e->getMessage()]);
}

// Close connection only for MySQL
if (!getenv('VERCEL')) {
    $conn->close();
}
