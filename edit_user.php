<?php
require_once 'classes/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new database();

if (!isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("No user ID provided.");
}

$user_id = $_GET['id'] ?? $_POST['user_id'];
$user = $db->getUserById($user_id);

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = trim($_POST['first_name']);
    $lastname = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if ($db->updateUser($firstname, $lastname, $username, $email, $role, $user_id)) {
        header("Location: users.php");
        exit();
    } else {
        $error = "Error updating user.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 30px;
            margin-top: 60px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        h2 { color: #0046af; margin-bottom: 25px; font-weight: 700; }
        .btn-primary { background-color: #0046af; border: none; }
        .btn-primary:hover { background-color: #003d99; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit User</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">

        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="inventory_staff" <?= $user['role'] === 'inventory_staff' ? 'selected' : '' ?>>Inventory Staff</option>
                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
