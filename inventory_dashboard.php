<?php
session_start();
require_once('classes/database.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit();
}

if ($_SESSION['role'] !== 'inventory_staff') {
    header('Location: login.php');
    exit();
}

$full_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];

$db = new database();
$totalProducts = $db->getTotalProducts();
$totalCategories = $db->getTotalCategories();
$lowStockProducts = $db->getLowStockProducts();
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
            background-color: #f4f6f9;
        }
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
        background-color: #003d80;
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
  margin-left: 260px;
  padding: 40px 30px;
}
 
 
    h1 {
        color: #0046af;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 50px;
    }
 
 
        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
 
        .card {
            background: white;
            padding: 20px;
            flex: 1 1 200px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
 
        .card h3 {
    color: #2c3e50;
    font-size: 1.1rem;
    text-transform: uppercase;
    margin-bottom: 10px;
}
 
.card p {
    font-size: 28px;
    font-weight: 700;
}
 
.table-section h3 {
    color: #c0392b;
}
 
        .table-section {
            background: white;
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
 
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
 
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
 
        th {
            background:rgb(0, 111, 222);
            color: white;
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
 
<!-- Main Content -->
<div class="main-content">
    <h1 class="mb-4 fw-bold">Welcome, <?= htmlspecialchars($full_name) ?>!</h1>
 
    <!-- Dashboard Summary Cards -->
    <div class="cards">
        <div class="card border-start border-5 border-primary">
            <h3 class="text-uppercase text-secondary">Total Products</h3>
            <p class="text-primary"><?= $totalProducts ?></p>
        </div>
        <div class="card border-start border-5 border-warning">
            <h3 class="text-uppercase text-secondary">Total Categories</h3>
            <p class="text-warning"><?= $totalCategories ?></p>
        </div>
    </div>
 
    <!-- Low Stock Table -->
    <div class="table-section mt-4">
        <h3 class="mb-3 text-danger fw-semibold">Low Stock Products (≤10)</h3>
        <?php if (count($lowStockProducts) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Product Name</th>
                    <th>Stock Left</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStockProducts as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                    <td><?= $product['product_stock'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="text-success">All stocks are sufficient.</p>
        <?php endif; ?>
    </div>
</div>
 
<script>
  document.querySelectorAll('.has-submenu > a').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      this.parentElement.classList.toggle('active');
    });
  });
</script>
 
</body>
</html>