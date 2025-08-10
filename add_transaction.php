<?php
session_start();
require_once('classes/database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff') {
    header("Location: login.php");
    exit();
}

$db = new database();
$message = "";

// Handle form submission
if (isset($_POST['add_transaction'])) {
    $type = $_POST['type'];
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    $remarks = $_POST['remarks'];

    if ($type && $product_id && $quantity > 0) {
        $result = $db->addInventoryTransaction($type, $product_id, $quantity, $remarks);
        $message = $result ? "Transaction successfully added." : "Failed to add transaction.";
    } else {
        $message = "Please fill out all fields.";
    }
}

$products = $db->getAllProducts();
$transactions = $db->getAllTransactions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Inventory Transaction</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
       body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
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

        .container {
            margin-left: 240px;
            padding: 40px 50px;
        }

        h1, h2 {
            color: #0046af;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 36px;
        }

        .card-form {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 40px;
            max-width: 700px;
            border-top: 5px solid #ffc107;
        }

        label {
            font-weight: 500;
            color: #333;
            margin-top: 15px;
        }

        input, select {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            padding: 10px 12px;
            margin-top: 5px;
            width: 100%;
        }

        input:focus, select:focus {
            border-color: #0046af;
            box-shadow: 0 0 0 2px rgba(0, 70, 175, 0.1);
            outline: none;
        }

        input[type="submit"] {
            width: auto;
            background: #0046af;
            color: #fff;
            border: none;
            margin-top: 20px;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        input[type="submit"]:hover {
            background: #003d80;
        }

        .success-message {
            color: green;
            font-weight: 500;
            margin-bottom: 20px;
        }

        table {
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        th {
            background-color: #0046af;
            color: white;
            padding: 14px 10px;
            text-align: center;
            font-weight: 600;
        }

        td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #eee;
            color: #333;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        a {
            color: #0046af;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
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

<div class="container">
    <h1>Add Inventory Transaction</h1>
    <?php if ($message): ?>
        <p class="success-message"><?= $message; ?></p>
    <?php endif; ?>
    <div class="card-form">
        <form method="POST">
            <label for="type">Type</label>
            <select name="type" id="type" required onchange="toggleAdjustment(this.value)">
                <option value="">Select</option>
                <option value="Add">Add (Stock In)</option>
                <option value="Remove">Remove (Stock Out)</option>
                <option value="Sale">Sale</option>
                <option value="Return">Return</option>
                <option value="Adjustment">Adjustment</option>
            </select>

            <label for="product">Product</label>
            <select name="product_id" id="product" required>
                <option value="">Select Product</option>
                <?php foreach ($products as $row): ?>
                    <option value="<?= $row['products_id']; ?>">
                        <?= htmlspecialchars($row['product_name']); ?> (Stock: <?= $row['product_stock']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="quantityInput" id="quantityLabel">Quantity</label>
            <input type="number" name="quantity" id="quantityInput" min="1" required>

            <label for="remarks">Remarks</label>
            <input type="text" name="remarks" id="remarks">

            <input type="submit" name="add_transaction" value="Save">
        </form>
    </div>

    <h2>Transaction History</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Remarks</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction['transaction_id']; ?></td>
                    <td><?= htmlspecialchars($transaction['product_name']); ?></td>
                    <td><?= $transaction['transaction_type']; ?></td>
                    <td><?= $transaction['quantity']; ?></td>
                    <td><?= htmlspecialchars($transaction['remarks']); ?></td>
                    <td><?= $transaction['transaction_date']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Submenu toggle fix
    document.querySelectorAll('.has-submenu > a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.classList.toggle('active');
        });
    });

    function toggleAdjustment(val) {
        const label = document.getElementById('quantityLabel');
        const input = document.getElementById('quantityInput');
        if (val === 'Adjustment') {
            label.textContent = 'New Stock Value';
            input.min = 0;
        } else {
            label.textContent = 'Quantity';
            input.min = 1;
        }
    }
</script>
</body>
</html>
