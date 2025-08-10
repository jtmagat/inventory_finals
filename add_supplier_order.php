<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || 
    ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff')) {
    header("Location: login.php");
    exit();
}

$db = new database();
$suppliers = $db->getAllSuppliers();

$sweetAlert = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_id = $_POST['supplier_id'];
    $order_date = $_POST['order_date'];
    $expected_date = $_POST['expected_delivery_date'];
    $total_cost = $_POST['total_cost'];
    $status = $_POST['order_status'];

    if ($db->addSupplierOrder($supplier_id, $order_date, $expected_date, $total_cost, $status)) {
        $sweetAlert = "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Order Added!',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'supplier_orders.php';
        });
        </script>";
    } else {
        $sweetAlert = "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Failed to add order.',
            showConfirmButton: true
        });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Supplier Order</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
</head>
<body>

<div class="main-content" style="margin-left: 240px; padding: 30px;">
    <h2>Add Supplier Order</h2>

    <form method="POST">
        <div class="mb-3">
            <label>Supplier</label>
            <select name="supplier_id" class="form-select" required>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['supplier_id'] ?>"><?= htmlspecialchars($supplier['supplier_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Order Date</label>
            <input type="date" name="order_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Expected Delivery Date</label>
            <input type="date" name="expected_delivery_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Total Cost (₱)</label>
            <input type="number" name="total_cost" step="0.01" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="order_status" class="form-select">
                <option value="Pending">Pending</option>
                <option value="Delivered">Delivered</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Add Order</button>
        <a href="supplier_orders.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?= $sweetAlert ?>
</body>
</html>
