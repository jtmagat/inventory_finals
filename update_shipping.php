<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff')) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'], $_GET['action'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$action = $_GET['action'];

$valid_actions = ['shipped' => 'Shipped', 'delivered' => 'Delivered'];

if (!array_key_exists($action, $valid_actions)) {
    header("Location: orders.php");
    exit();
}

$db = new database();
$db->updateOrderStatus($order_id, $valid_actions[$action]);

header("Location: orders.php");
exit();
?>
