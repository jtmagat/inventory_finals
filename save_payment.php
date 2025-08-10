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

$order_id = $_POST['order_id'];
$method = $_POST['payment_method'];
$amount = $_POST['amount_paid'];

$db = new database();
$con = $db->opencon();

try {
    $con->beginTransaction();

    // Use database.php methods
    $db->addPayment($order_id, $method, $amount);
    $db->updateOrderStatus($order_id);

    $con->commit();

    header("Location: orders.php?payment=success");
    exit();
} catch (Exception $e) {
    $con->rollBack();
    die("Payment error: " . $e->getMessage());
}
?>
