<?php
include 'config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['name']) || !isset($input['category']) || !isset($input['price'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid data"]);
        exit;
    }

    $name = $input['name'];
    $category = $input['category'];
    $description = $input['description'] ?? '';
    $price = $input['price'];
    $image_color = $input['image_color'] ?? '#8B4513';

    if (getenv('VERCEL')) {
        // SQLite version
        $stmt = $conn->prepare("INSERT INTO food_items (name, category, description, price, image_color) VALUES (?, ?, ?, ?, ?)");
        $success = $stmt->execute([$name, $category, $description, $price, $image_color]);
    } else {
        // MySQL version
        $sql = "INSERT INTO food_items (name, category, description, price, image_color) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssds", $name, $category, $description, $price, $image_color);
        $success = $stmt->execute();
    }

    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to add food item"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}

// Close connection only for MySQL
if (!getenv('VERCEL')) {
    $conn->close();
}
