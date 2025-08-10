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

$selectedCat = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$categories = $db->getAllCategories();
$products = $db->getFilteredProducts($selectedCat, $search);
$lowStockProducts = $db->getLowStockProducts($selectedCat);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
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
        padding: 40px 20px;
    }

    .main-content h2 {
        color: #0046af;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 50px;
    }

    .card {
        border: none;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        color: #0046af;
        font-weight: 600;
    }

    .btn-primary {
        background-color: #0046af;
        border: none;
    }

    .btn-primary:hover {
        background-color: #003b91;
    }

    .low-stock-label {
        color: red;
        font-weight: bold;
        margin-left: 5px;
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
    <h2>Products</h2>

    <div class="mb-3">
        <a href="add_product.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
    </div>

    <form method="GET" class="d-flex align-items-center mb-3 gap-3 flex-wrap position-relative">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control search-box" placeholder="Search products..." autocomplete="off" />
        <select name="category_id" class="form-select" onchange="this.form.submit()" style="max-width: 200px;">
            <option value="0">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" <?= $selectedCat == $cat['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline-primary">Search</button>
    </form>

    <div class="mb-3">
        <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#lowStockList">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Show Low Stock Products
        </button>
    </div>
    <div class="collapse mb-4" id="lowStockList">
        <div class="card card-body border border-danger">
            <h5 class="text-danger mb-3"><i class="bi bi-box-seam-fill me-1"></i> Low Stock Items</h5>
            <?php if (count($lowStockProducts) > 0): ?>
                <?php foreach ($lowStockProducts as $prod): ?>
                    <div class="text-danger mb-1">⚠️ <?= htmlspecialchars($prod['product_name']) ?> — Stock: <?= $prod['product_stock'] ?></div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-success">✅ All products are sufficiently stocked.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $prod): ?>
                <?php $isLow = (int)$prod['product_stock'] <= 5; ?>
                <div class="col-md-4 mb-4">
                    <div class="card <?= $isLow ? 'border border-danger' : '' ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($prod['product_name']) ?></h5>
                            <p>Price: <strong>﷼<?= number_format($prod['product_price'], 2) ?></strong></p>
                            <p>
                                Stock: <?= $prod['product_stock'] ?>
                                <?= $isLow ? '<span class="low-stock-label">⚠️ Low</span>' : '' ?>
                            </p>
                            <a href="edit_product.php?product_id=<?= $prod['products_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_product.php?product_id=<?= $prod['products_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">No products found.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
