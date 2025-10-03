<?php
include 'config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid data"]);
        exit;
    }

    $id = $input['id'];

    if (getenv('VERCEL')) {
        // SQLite version
        $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
        $success = $stmt->execute([$id]);
    } else {
        // MySQL version
        $sql = "DELETE FROM food_items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
    }

    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete food item"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}

// Close connection only for MySQL
if (!getenv('VERCEL')) {
    $conn->close();
}
