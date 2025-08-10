<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    // Redirect to orders page if no order_id is given
    header("Location: orders.php");
    exit();
}

$orderId = $_GET['order_id'];

$db = new database();

$order = $db->getOrderById($orderId);
if (!$order) {
    echo "<p style='color:red;'>Order not found. <a href='orders.php'>Go back to Orders</a></p>";
    exit();
}

$items = $db->getOrderItems($orderId);
?>

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #ecf0f1;
            padding: 40px;
        }
        .order {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            color: #0046af;
            font-weight: 600;
        }
        h4 {
            color: #2c3e50;
            margin-top: 30px;
        }
        p {
            margin-bottom: 5px;
            font-size: 15px;
        }
        strong {
            color: #2c3e50;
        }
        table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ccc;
        }
        th {
            background-color: #0046af;
            color: white;
        }
        td {
            font-size: 14px;
        }
        .back-btn {
            margin-top: 25px;
            display: inline-block;
            background-color: #ffc107;
            color: #000;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background-color: #e0aa00;
            color: #fff;
        }
    </style>
</head>
<body>
 
<div class="order">
    <h2>Order #<?= htmlspecialchars($order['order_id']) ?></h2>
    <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
    <p><strong>Total Amount:</strong> ر.س <?= number_format($order['total_amount'], 2) ?></p>
 
    <h4>Items</h4>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price (ر.س)</th>
                <th>Subtotal (ر.س)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($item['price'], 2) ?></td>
                    <td>₱<?= number_format($item['subtotal'], 2) ?></td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
 
    <?php
        $backPage = ($_SESSION['role'] === 'customer') ? 'my_orders.php' : 'orders.php';
    ?>
    <a href="<?= $backPage ?>" class="back-btn">Back</a>
    </div>
 
</body>
</html>
 
 