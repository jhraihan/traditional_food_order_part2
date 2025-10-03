<?php
include 'config.php';

// Get JSON data from frontend
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(["error" => "No data received"]);
    exit;
}

try {
    if (getenv('VERCEL')) {
        // SQLite version - Start transaction
        $conn->beginTransaction();

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, total_amount) VALUES (?, ?, ?, ?)");
        $stmt->execute([$input['customer_name'], $input['customer_email'], $input['customer_phone'], $input['total']]);
        $order_id = $conn->lastInsertId();

        // Insert order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, food_item_id, food_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
        foreach ($input['items'] as $item) {
            $stmt->execute([$order_id, $item['id'], $item['name'], $item['quantity'], $item['price']]);
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            "success" => true,
            "message" => "Order placed successfully!",
            "order_id" => $order_id
        ]);
    } else {
        // MySQL version - Start transaction
        $conn->begin_transaction();

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, total_amount) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $input['customer_name'], $input['customer_email'], $input['customer_phone'], $input['total']);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Insert order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, food_item_id, food_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
        foreach ($input['items'] as $item) {
            $stmt->bind_param("iisid", $order_id, $item['id'], $item['name'], $item['quantity'], $item['price']);
            $stmt->execute();
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            "success" => true,
            "message" => "Order placed successfully!",
            "order_id" => $order_id
        ]);
    }
} catch (Exception $e) {
    // Rollback on error
    if (getenv('VERCEL')) {
        $conn->rollback();
    } else {
        $conn->rollback();
    }
    echo json_encode(["error" => "Order failed: " . $e->getMessage()]);
}

// Close connection only for MySQL
if (!getenv('VERCEL')) {
    $conn->close();
}
