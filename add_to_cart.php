<?php
session_start();
require_once('classes/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new database();

    $user_id = $_SESSION['user_id'];
    $products_id = isset($_POST['products_id']) ? (int)$_POST['products_id'] : 0;
    $cart_quantity = isset($_POST['cart_quantity']) ? max(1, (int)$_POST['cart_quantity']) : 1;

    if ($products_id > 0) {
        $db->addToCart($user_id, $products_id, $cart_quantity);
        $_SESSION['cart_message'] = "✅ Item successfully added to cart!";
    } else {
        $_SESSION['cart_message'] = "❌ Failed to add item. Please try again.";
    }

    $redirect = $_SERVER['HTTP_REFERER'] ?? 'browse_products.php';
    header("Location: $redirect");
    exit();
} else {
    header("Location: browse_products.php");
    exit();
}
