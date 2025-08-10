<?php
session_start();
require_once('classes/database.php');

// Check if customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$first_name = $_SESSION['first_name'] ?? 'Customer';
$last_name = $_SESSION['last_name'] ?? '';
$full_name = $first_name . ' ' . $last_name;

$db = new Database();
$orders = $db->getOrdersByUser($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #0046af;
            color: white;
            position: fixed;
            padding: 20px;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffc107;
            padding-left: 10px;
            margin-top: 30px;
        }

        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin: 15px 0; }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover { background-color: #0056b3; }

        .main-content {
            margin-left: 260px;
            padding: 40px 20px;
        }

        .main-content h1 {
            margin-bottom: 20px;
            color: #0046af;
            font-weight: 700;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #0046af;
            color: white;
        }

        .btn-view {
            background-color: #0046af;
            color: white;
            font-weight: 500;
        }

        .btn-view:hover {
            background-color: #003080;
        }

        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-completed {
            background-color: #28a745;
            color: white;
        }

        .badge-cancelled {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="bi bi-person-circle"></i> Customer Panel</h2>
    <ul>
        <li><a href="customer_dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
        <li><a href="browse_products.php"><i class="bi bi-bag"></i> Browse Products</a></li>
        <li><a href="cart.php"><i class="bi bi-cart3"></i> View Cart</a></li>
        <li><a href="my_orders.php"><i class="bi bi-receipt"></i> My Orders</a></li>
        <li><a href="profile.php"><i class="bi bi-person-gear"></i> Profile</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>📦 My Orders</h1>

    <div class="card">
        <h4>Order History</h4>
        <?php if (!empty($orders)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_id']); ?></td>
                            <td><?= htmlspecialchars(date('F j, Y', strtotime($order['order_date']))); ?></td>
                            <td>
                                <?php
                                    $status = strtolower($order['order_status']);
                                    $badgeClass = $status === 'pending' ? 'badge-pending' : ($status === 'completed' ? 'badge-completed' : 'badge-cancelled');
                                ?>
                                <span class="badge-status <?= $badgeClass ?>">
                                    <?= ucfirst($status); ?>
                                </span>
                            </td>
                            <td>₱<?= number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <a href="view_order_items.php?order_id=<?= $order['order_id']; ?>" class="btn btn-sm btn-view">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven’t placed any orders yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
