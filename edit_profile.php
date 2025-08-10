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

// Fetch current user data
$user = $db->getUserById($user_id);

if (!$user) {
    echo "<script>alert('User not found.'); window.location='login.php';</script>";
    exit();
}

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name     = trim($_POST['first_name']);
    $last_name      = trim($_POST['last_name']);
    $username       = trim($_POST['username']);
    $email          = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $address        = trim($_POST['address']);

    if (empty($first_name) || empty($last_name) || empty($username) || empty($email)) {
        $error = "Please fill in all required fields.";
    } else {
        if ($db->updateUserProfile($user_id, $first_name, $last_name, $username, $email, $contact_number, $address)) {
            $success = "Profile updated successfully.";
            $user = $db->getUserById($user_id); // Refresh user data
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}$db = new database();
$user_id = $_SESSION['user_id'];

// Fetch current user data
$user = $db->getUserById($user_id);

if (!$user) {
    echo "User not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name     = $_POST['first_name'] ?? '';
    $last_name      = $_POST['last_name'] ?? '';
    $username       = $_POST['username'] ?? '';
    $email          = $_POST['email'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $address        = $_POST['address'] ?? '';

    // Call update function
    if ($db->updateUserProfile($user_id, $first_name, $last_name, $username, $email, $contact_number, $address)) {
        // Redirect back to profile page after saving
        header("Location: profile.php");
        exit;
    } else {
        $error = "Failed to update profile. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
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
        th { background-color: #f8f9fa; width: 200px; }
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
    <h1>✏️ Edit Profile</h1>

    <div class="card">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <table class="table table-bordered">
                <tr>
                    <th>First Name</th>
                    <td><input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required></td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required></td>
                </tr>
                <tr>
                    <th>Contact Number</th>
                    <td><input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($user['contact_number']) ?>"></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><textarea name="address" class="form-control"><?= htmlspecialchars($user['address']) ?></textarea></td>
                </tr>
            </table>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
            <a href="profile.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
        </form>
    </div>
</div>

</body>
</html>
