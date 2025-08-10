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

$db = new database();
$suppliers = $db->getAllSuppliers();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suppliers</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #ecf0f1;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: rgb(0, 70, 175);
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

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #0056b3;
        }

        .has-submenu > a::after {
            content: "\25BC";
            float: right;
            font-size: 0.7rem;
            transition: transform 0.3s;
        }

        .submenu {
            list-style: none;
            padding-left: 20px;
            display: none;
        }

        .has-submenu.active .submenu {
            display: block;
        }

        .has-submenu.active > a::after {
            transform: rotate(180deg);
        }

        .main-content {
            margin-left: 240px;
            padding: 30px;
        }

        .main-content h2 {
            color: #0046af;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .main-content p {
            color: #555;
            margin-bottom: 25px;
        }

        .add-btn {
            margin-bottom: 15px;
            display: inline-block;
            padding: 8px 15px;
            background: #ffc107;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .add-btn:hover {
            background: #e0a800;
        }

        .supplier-order-btn {
            margin-left: 10px;
            background: #28a745;
            color: white;
            transition: background 0.3s;
        }

        .supplier-order-btn:hover {
            background: #218838;
        }

        .table-container {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #0046af;
            color: white;
        }

        td {
            font-size: 0.95rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .btn-sm i {
            font-size: 1rem;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="bi bi-speedometer2"></i> <?= ($_SESSION['role']==='admin') ? 'Admin Panel' : 'Inventory Panel'; ?></h2>
    <ul>
        <li><a href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'inventory_dashboard.php'; ?>"><i class="bi bi-house-door"></i> Dashboard</a></li>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="users.php"><i class="bi bi-people"></i> Users</a></li>
        <?php endif; ?>
        <li><a href="products.php"><i class="bi bi-box"></i> Products</a></li>
        <li><a href="orders.php"><i class="bi bi-cart"></i> Orders</a></li>

        <li class="has-submenu">
            <a href="#"><i class="bi bi-receipt"></i> Sales</a>
            <ul class="submenu">
                <li><a href="add_transaction.php">Inventory Transactions</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="sales_report.php">Sales Report</a></li>
                <?php endif; ?>
            </ul>
        </li>

        <li class="has-submenu">
            <a href="#"><i class="bi bi-cash-coin"></i> Transactions</a>
            <ul class="submenu">
                <li><a href="payments.php">Payment and Invoicing</a></li>
                <li><a href="shipping_and_delivery.php">Shipping and Delivery</a></li>
            </ul>
        </li>

        <li><a href="suppliers.php"><i class="bi bi-truck"></i> Suppliers</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h2>Suppliers</h2>
    <p>Manage all your suppliers here. You can add, edit, or delete suppliers.</p>

    <a href="add_supplier.php" class="add-btn">Add New Supplier</a>
    <a href="supplier_orders.php" class="add-btn supplier-order-btn">Supplier Orders</a>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($suppliers) > 0): ?>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?= htmlspecialchars($supplier['supplier_id']) ?></td>
                            <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                            <td><?= htmlspecialchars($supplier['supplier_phonenumber']) ?></td>
                            <td><?= htmlspecialchars($supplier['supplier_email']) ?></td>
                            <td>
                                <a href="edit_supplier.php?id=<?= $supplier['supplier_id'] ?>" class="btn btn-sm btn-primary me-1" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="delete_supplier.php?id=<?= $supplier['supplier_id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this supplier?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No suppliers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.querySelectorAll('.has-submenu > a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.classList.toggle('active');
        });
    });
</script>

</body>
</html>
