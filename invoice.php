<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$con = new database();

$order_id = $_GET['order_id'] ?? '';

$order = $con->getOrderDetailsById($order_id);
if (!$order) {
    die('Order not found.');
}

$items = $con->getOrderItems($order_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 40px;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .invoice-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #0046af;
            margin-bottom: 20px;
        }

        .invoice-header p {
            margin: 0;
        }

        .table th {
            background-color: #0046af;
            color: white;
        }

        .total-section {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
        }

        .print-btn {
            display: inline-block;
            background-color: #0046af;
            color: white;
            padding: 6px 12px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            margin-top: 20px;
            float: right;
        }

        .print-btn:hover {
            background-color: #003b90;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <h2>Invoice</h2>

    <div class="invoice-header mb-4">
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
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

    <div class="total-section">
        <strong>Total: ₱<?= number_format($order['total_amount'], 2) ?></strong>
    </div>

    <button onclick="window.print()" class="print-btn">🖨️ Print Invoice</button>
</div>

</body>
</html>
