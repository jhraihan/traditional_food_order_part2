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
        // SQLite version - Start transaction
        $conn->beginTransaction();

        // Delete order items first
        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$id]);

        // Delete order
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $success = $stmt->execute([$id]);

        // Commit transaction
        $conn->commit();
    } else {
        // MySQL version - Start transaction
        $conn->begin_transaction();

        // Delete order items first
        $sql1 = "DELETE FROM order_items WHERE order_id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $id);
        $stmt1->execute();

        // Delete order
        $sql2 = "DELETE FROM orders WHERE id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $id);
        $success = $stmt2->execute();

        // Commit transaction
        $conn->commit();
    }

    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete order"]);
    }
} catch (Exception $e) {
    // Rollback on error
    if (getenv('VERCEL')) {
        $conn->rollback();
    } else {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}

// Close connection only for MySQL
if (!getenv('VERCEL')) {
    $conn->close();
}
