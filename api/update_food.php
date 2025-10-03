<?php
include 'config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['id']) || !isset($input['name']) || !isset($input['category']) || !isset($input['price'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid data"]);
        exit;
    }

    $id = $input['id'];
    $name = $input['name'];
    $category = $input['category'];
    $description = $input['description'] ?? '';
    $price = $input['price'];
    $image_color = $input['image_color'] ?? '#8B4513';

    if (getenv('VERCEL')) {
        // SQLite version
        $stmt = $conn->prepare("UPDATE food_items SET name = ?, category = ?, description = ?, price = ?, image_color = ? WHERE id = ?");
        $success = $stmt->execute([$name, $category, $description, $price, $image_color, $id]);
    } else {
        // MySQL version
        $sql = "UPDATE food_items SET name = ?, category = ?, description = ?, price = ?, image_color = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsi", $name, $category, $description, $price, $image_color, $id);
        $success = $stmt->execute();
    }

    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update food item"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}

// Close connection only for MySQL
if (!getenv('VERCEL')) {
    $conn->close();
}
