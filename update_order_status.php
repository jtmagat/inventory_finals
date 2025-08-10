<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Allow both Admin and Inventory Staff
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $new_status = 'Completed'; // hardcoded here; can be dynamic if needed

    $db = new database();
    $db->updateOrderStatus($order_id, $new_status);

    header("Location: orders.php");
    exit();
} else {
    header("Location: orders.php");
    exit();
}
?>
