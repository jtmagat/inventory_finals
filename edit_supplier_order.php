<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || 
    ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff')) {
    header("Location: login.php");
    exit();
}

$con = new database();
$sweetAlert = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $expected_delivery = $_POST['expected_delivery'];
    $total_cost = $_POST['total_cost'];

    if ($con->updateSupplierOrder($id, $expected_delivery, $total_cost)) {
        $sweetAlert = "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Order Updated!',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'supplier_orders.php';
        });
        </script>";
    }
}

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    header("Location: supplier_orders.php");
    exit();
}

$order = $con->getSupplierOrderById($id);

if (!$order) {
    echo "Order not found!";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Order</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
</head>
<body class="container mt-4">
    <h2>Edit Supplier Order</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $order['supplier_order_id'] ?>">
        <div class="mb-3">
            <label>Expected Delivery:</label>
            <input type="date" name="expected_delivery" class="form-control" value="<?= $order['expected_delivery_date'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Total Cost:</label>
            <input type="number" name="total_cost" step="0.01" class="form-control" value="<?= $order['total_cost'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Order</button>
        <a href="supplier_orders.php" class="btn btn-secondary">Cancel</a>
    </form>
    <?= $sweetAlert ?>
</body>
</html>
