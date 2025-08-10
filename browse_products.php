<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$db = new database();

$selectedCat = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$categories = $db->getAllCategories();
$products = $db->getFilteredProducts($selectedCat, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Products</title>
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

    .main-content {
        margin-left: 260px;
        padding: 40px 20px;
    }

    .main-content h2 {
        color: #0046af;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 40px;
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

    .search-box {
        max-width: 300px;
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
    <h2>Browse Products</h2>

    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="flash">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['cart_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>

    <form method="GET" class="d-flex align-items-center mb-4 gap-3 flex-wrap">
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

    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $prod): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($prod['product_name']) ?></h5>
                            <p>Price: <strong>₱<?= number_format($prod['product_price'], 2) ?></strong></p>
                            <p>Stock: <?= (int)$prod['product_stock'] ?></p>
                            <form method="POST" action="add_to_cart.php" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="products_id" value="<?= htmlspecialchars($prod['products_id']) ?>">
                                <input type="number" name="cart_quantity" value="1" min="1" max="<?= (int)$prod['product_stock'] ?>" class="form-control form-control-sm" style="width: 80px;" required>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            </form>
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

<script src="./bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
