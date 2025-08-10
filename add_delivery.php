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

$order_id = $_GET['order_id'];
$user_id = $_GET['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Shipping Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">
<div class="container bg-white p-4 rounded shadow-sm">
    <h3 class="mb-4">Add Shipping & Delivery Info</h3>

    <form action="process_delivery.php" method="POST">
        <!-- These must be included and correct -->
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

        <div class="mb-3">
            <label class="form-label">Tracking Number</label>
            <input type="text" name="tracking_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Shipping Method</label>
            <select name="shipping_method" class="form-select" required>
                <option value="">Select Method</option>
                <option value="LBC">LBC</option>
                <option value="J&T">J&T</option>
                <option value="Ninja Van">Ninja Van</option>
                <option value="Pick-up">Pick-up</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Estimated Delivery Date</label>
            <input type="date" name="estimated_delivery_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Delivery Status</label>
            <select name="delivery_status" class="form-select" required>
                <option value="Pending">Pending</option>
                <option value="Shipped">Shipped</option>
                <option value="Delivered">Delivered</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Delivery Info</button>
        <a href="orders.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>