<?php
require_once('classes/database.php');
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    $required_fields = [
        'order_id',
        'user_id',
        'tracking_number',
        'shipping_method',
        'estimated_delivery_date',
        'delivery_status'
    ];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            throw new Exception("Missing required field: $field");
        }
    }

    $order_id = trim($_POST['order_id']);
    $user_id = trim($_POST['user_id']);
    $tracking_number = trim($_POST['tracking_number']);
    $shipping_method = trim($_POST['shipping_method']);
    $estimated_delivery_date = trim($_POST['estimated_delivery_date']);
    $delivery_status = trim($_POST['delivery_status']);

    $db = new database();
    $result = $db->addShippingDelivery($order_id, $user_id, $tracking_number, $shipping_method, $estimated_delivery_date, $delivery_status);

    if ($result) {
        header("Location: orders.php?status=delivery_added");
        exit();
    } else {
        throw new Exception("Failed to add shipping/delivery details.");
    }

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<a href='javascript:history.back()'>Go back</a>";
    exit();
}
?>
