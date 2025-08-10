<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new database();
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['products'])) {
        $products = [];

        foreach ($_POST['products'] as $product_id) {
            $quantity = $_POST['quantities'][$product_id];
            $products[$product_id] = $quantity;
        }

        $result = $db->processOrder($user_id, $products);

        if ($result === true) {
            header("Location: view_order_items.php");
            exit();
        } else {
            echo "Error: $result";
        }
    } else {
        echo "No products selected.";
    }
} else {
    echo "Invalid request method.";
}
?>
