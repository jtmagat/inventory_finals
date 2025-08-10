<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new database();
$sweetAlert = "";

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Use method from database.php
    if ($db->deleteSupplierOrderById($order_id)) {
        $sweetAlert = "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Order Deleted!',
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
            title: 'Error deleting order!',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'supplier_orders.php';
        });
        </script>";
    }
} else {
    header("Location: supplier_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Deleting...</title></head>
<body>
<?= $sweetAlert ?>
</body>
</html>
