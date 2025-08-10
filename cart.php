<?php
session_start();
require_once('classes/database.php');

// Check kung naka-login at customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$user_id = $_SESSION['user_id'];

// Handle item removal
if (isset($_GET['remove'])) {
    $cart_id = (int) $_GET['remove'];
    $db->removeFromCart($cart_id);
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Item removed from cart!'];
    header("Location: cart.php");
    exit();
}

// Kunin cart items
$cartItems = $db->getCartItems($user_id);

// Compute total
$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += $item['product_price'] * $item['cart_quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert CDN -->
    <style>
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
            background-color: #0056b3;
        }
        .main-content {
            margin-left: 260px;
            padding: 40px 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        thead {
            background-color: #0046af;
            color: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .checkout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            float: right;
            margin-top: 10px;
        }
        .checkout-btn:hover {
            background-color: #218838;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
        }
        .back-btn:hover {
            background-color: #5a6268;
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
    <h1>🛒 My Cart</h1>

    <?php if (!empty($cartItems)): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['category_name']) ?></td>
                        <td>₱<?= number_format($item['product_price'], 2) ?></td>
                        <td><?= (int)$item['cart_quantity'] ?></td>
                        <td>₱<?= number_format($item['product_price'] * $item['cart_quantity'], 2) ?></td>
                        <td>
                            <a href="cart.php?remove=<?= $item['cart_id'] ?>" 
                             class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to remove this item?');">
                                Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th>₱<?= number_format($totalAmount, 2) ?></th>
                    <th></th>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-between mt-3">
            <a href="browse_products.php" class="back-btn">← Back</a>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Your cart is empty.</div>
        <a href="browse_products.php" class="back-btn">← Back</a>
    <?php endif; ?>
</div>

<?php
// SweetAlert Message
if (isset($_SESSION['alert'])) {
    $type = $_SESSION['alert']['type'];
    $message = $_SESSION['alert']['message'];
    echo "<script>
        Swal.fire({
            icon: '$type',
            title: '$message',
            showConfirmButton: false,
            timer: 1500
        });
    </script>";
    unset($_SESSION['alert']);
}
?>

</body>
</html>
