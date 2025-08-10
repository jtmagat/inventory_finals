<?php
session_start();
require_once('classes/database.php');

// Check if customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$user_id = $_SESSION['user_id'];

// Fetch user data
$user = $db->getUserById($user_id); // Ensure this method exists in database.php

if (!$user) {
    echo "<script>alert('User not found.'); window.location='login.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
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
        .profile-info th {
            width: 200px;
            background-color: #f8f9fa;
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
        <li><a href="profile.php" class="active"><i class="bi bi-person-gear"></i> Profile</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>👤 My Profile</h1>

    <div class="card">
        <table class="table table-bordered profile-info">
            <tr>
                <th>First Name</th>
                <td><?= htmlspecialchars($user['first_name']); ?></td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td><?= htmlspecialchars($user['last_name']); ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?= htmlspecialchars($user['username']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Contact Number</th>
                <td><?= htmlspecialchars($user['contact_number']); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?= htmlspecialchars($user['address']); ?></td>
            </tr>
            <tr>
                <th>Member Since</th>
                <td><?= date("F j, Y", strtotime($user['created_at'])); ?></td>
            </tr>
        </table>
        <a href="edit_profile.php" class="btn btn-primary mt-3"><i class="bi bi-pencil-square"></i> Edit Profile</a>
    </div>
</div>

</body>
</html>
