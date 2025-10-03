<?php
include 'config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['order_id']) || !isset($input['status'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid data"]);
        exit;
    }

    $order_id = $input['order_id'];
    $status = $input['status'];

    if (getenv('VERCEL')) {
        // SQLite version
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $success = $stmt->execute([$status, $order_id]);
    } else {
        // MySQL version
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        $success = $stmt->execute();
    }

    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Update failed"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}

// Close connection only for MySQL
if (!getenv('VERCEL')) {
    $conn->close();
}
