<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cart_id'])) {
        $cartId = $_POST['cart_id'];
        $userId = $_SESSION['user_id'];

        $db = new database();

        // Make sure the cart item belongs to the logged-in user
        $cartItem = $db->getCartItemById($cartId);

        if ($cartItem && $cartItem['user_id'] == $userId) {
            $db->removeFromCart($cartId);
        }
    }
}

header("Location: cart.php");
exit();
?>
