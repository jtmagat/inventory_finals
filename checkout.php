<?php
session_start();
require_once('classes/database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$db = new Database();
$response = $db->checkoutOrder($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
if ($response === "success") {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Order Placed!',
            text: 'Thank you for your purchase.',
            confirmButtonText: 'View Orders'
        }).then(() => {
            window.location.href = 'orders.php';
        });
    </script>";

} elseif ($response === "empty_cart") {
    echo "<script>
        Swal.fire({
            icon: 'info',
            title: 'Cart is Empty!',
            text: 'You need to add items to your cart first.'
        }).then(() => {
            window.location.href = 'cart.php';
        });
    </script>";

} elseif ($response === "stock_error") {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Insufficient Stock',
            text: 'One or more products in your cart exceed available stock.'
        }).then(() => {
            window.location.href = 'cart.php';
        });
    </script>";

} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Checkout Failed',
            text: 'Something went wrong. Please try again.'
        }).then(() => {
            window.location.href = 'cart.php';
        });
    </script>";
}
?>
</body>
</html>
