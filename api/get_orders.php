<?php
include 'config.php';

try {
    if (getenv('VERCEL')) {
        // SQLite version
        $stmt = $conn->query("
            SELECT o.*, 
            (SELECT GROUP_CONCAT(oi.quantity || ' x ' || oi.food_name, ', ') 
             FROM order_items oi 
             WHERE oi.order_id = o.id) as items
            FROM orders o 
            ORDER BY o.created_at DESC
        ");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // MySQL version
        $sql = "SELECT o.*, 
                (SELECT GROUP_CONCAT(CONCAT(oi.quantity, ' x ', oi.food_name) SEPARATOR ', ') 
                 FROM order_items oi 
                 WHERE oi.order_id = o.id) as items
                FROM orders o 
                ORDER BY o.created_at DESC";

        $result = $conn->query($sql);
        $orders = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
    }

    echo json_encode($orders);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch orders: " . $e->getMessage()]);
}

// Close connection only for MySQL
if (!getenv('VERCEL')) {
    $conn->close();
}
