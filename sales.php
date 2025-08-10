<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header("Location: login.php");
    exit();
}

$db = new database();
$products = $db->getAllProducts();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $quantity = intval($_POST['quantity']);

        if ($product_id && $quantity > 0) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            $message = "Product added to cart.";
        }
    }

    if (isset($_POST['remove_from_cart'])) {
        $product_id = $_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
    }

    if (isset($_POST['place_order'])) {
        $staff_id = $_SESSION['user_id'];
        $items = $_SESSION['cart'];

        if (count($items) === 0) {
            $message = 'Cart is empty.';
        } else {
            $con = $db->opencon();
            $con->beginTransaction();
            try {
                $total = 0;

                foreach ($items as $pid => $qty) {
                    foreach ($products as $prod) {
                        if ($prod['products_id'] == $pid) {
                            $total += $prod['product_price'] * $qty;
                        }
                    }
                }

                $order_id = $db->insertOrder($staff_id, $total);

                foreach ($items as $pid => $qty) {
                    foreach ($products as $prod) {
                        if ($prod['products_id'] == $pid) {
                            $price = $prod['product_price'];
                            $db->insertOrderItem($order_id, $pid, $qty, $price);
                        }
                    }
                }

                $con->commit();
                $_SESSION['cart'] = [];
                $message = "Order placed successfully!";
            } catch (Exception $e) {
                $con->rollBack();
                $message = "Order failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales - Create Order</title>
    <style>
        body { font-family: Arial, sans-serif; background: #ecf0f1; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px #ccc; }
        h1, h2 { color: #2c3e50; }
        label { margin-top: 10px; display: block; }
        input, select { padding: 8px; width: 100%; margin-bottom: 10px; border-radius: 4px; border: 1px solid #ccc; }
        input[type="number"] { width: 70px; display: inline-block; }
        input[type="submit"] { background: #2980b9; color: #fff; border: none; cursor: pointer; width: auto; }
        input[type="submit"]:hover { background: #3498db; }
        table { margin-top: 30px; border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #2980b9; color: #fff; }
        .success-message { color: green; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Create Sales Order</h1>
    <?php if ($message): ?>
        <p class="success-message"><?= $message ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Product</label>
        <select name="product_id" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?= $prod['products_id'] ?>">
                    <?= htmlspecialchars($prod['product_name']) ?> (Stock: <?= $prod['product_stock'] ?>, ₱<?= number_format($prod['product_price'],2) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <label>Quantity</label>
        <input type="number" name="quantity" min="1" required>
        <input type="submit" name="add_to_cart" value="Add to Cart">
    </form>

    <h2>Cart</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th><th>Remove</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $cart = $_SESSION['cart'];
        $total = 0;
        foreach ($cart as $pid => $qty):
            foreach ($products as $prod) {
                if ($prod['products_id'] == $pid) {
                    $subtotal = $prod['product_price'] * $qty;
                    $total += $subtotal;
        ?>
            <tr>
                <td><?= htmlspecialchars($prod['product_name']) ?></td>
                <td><?= $qty ?></td>
                <td>₱<?= number_format($prod['product_price'],2) ?></td>
                <td>₱<?= number_format($subtotal,2) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $pid ?>">
                        <input type="submit" name="remove_from_cart" value="Remove">
                    </form>
                </td>
            </tr>
        <?php } } endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right;">Total:</th>
                <th colspan="2">₱<?= number_format($total,2) ?></th>
            </tr>
        </tfoot>
    </table>

    <form method="POST">
        <input type="submit" name="place_order" value="Place Order">
    </form>
</div>
</body>
</html>
