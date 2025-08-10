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
$payments = $db->getAllPayments(); // Now using the method from database.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment and Invoicing</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: #0056b3;
        }

        .has-submenu > a::after {
            content: "\25BC";
            float: right;
            font-size: 0.7rem;
            transition: transform 0.3s;
        }

        .has-submenu.active > a::after {
            transform: rotate(180deg);
        }

        .submenu {
            list-style: none;
            padding-left: 20px;
            display: none;
        }

        .has-submenu.active .submenu {
            display: block;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px 20px;
        }

        h1 {
            color: rgb(0, 70, 175);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .table-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        table th, table td {
            vertical-align: middle;
        }

        .dataTables_filter input {
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 5px 10px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .btn-primary {
            background-color: #0046af;
            border: none;
        }

        .btn-primary:hover {
            background-color: #003b96;
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
    <h1>Payment and Invoicing</h1>
    <div class="table-container">
        <table id="paymentTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Payment Method</th>
                    <th>Amount Paid</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $pay): ?>
                    <tr>
                        <td><?= $pay['payment_id'] ?></td>
                        <td><?= $pay['order_id'] ?></td>
                        <td><?= htmlspecialchars($pay['username']) ?> (ID: <?= $pay['user_id'] ?>)</td>
                        <td><?= htmlspecialchars($pay['payment_method']) ?></td>
                        <td>﷼<?= number_format($pay['amount_paid'], 2) ?></td>
                        <td><?= date('M d, Y h:i A', strtotime($pay['payment_date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Scripts -->
<script src="./bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#paymentTable').DataTable({
            order: [[5, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search payments..."
            }
        });

        document.querySelectorAll('.has-submenu > a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('active');
            });
        });
    });
</script>

</body>
</html>