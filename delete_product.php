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

$con = new database();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id'])) {
    $products_id = (int)$_GET['product_id'];

    // Use method from database.php to check
    $product = $con->deleteProductById($products_id);

    if ($product) {
        if ($con->deleteProductById($products_id)) {
            echo "<script>
                alert('Product deleted successfully!');
                window.location.href='products.php?category_id={$product['category_id']}';
            </script>";
        } else {
            echo "<script>
                alert('Error deleting the product.');
                window.location.href='products.php?category_id={$product['category_id']}';
            </script>";
        }
    } else {
        echo "<script>
            alert('Product not found.');
            window.location.href='products.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href='products.php';
    </script>";
}
?>

