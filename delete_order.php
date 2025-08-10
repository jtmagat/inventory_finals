<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $db = new database();
    $db->softDeleteOrder($order_id);

    header("Location: orders.php");
    exit();
} else {
    header("Location: orders.php");
    exit();
}
?>

