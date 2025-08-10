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

// If form is submitted (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products_id = $_POST['products_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_stock = $_POST['product_stock'];
    $category_id = $_POST['category_id']; // hidden field in form

    $update = $con->updateProduct($product_name, $product_price, $product_stock, $category_id, $products_id);

    if ($update) {
        echo "<script>
            alert('Product updated successfully!');
            window.location.href='products.php?category_id=$category_id';
        </script>";
    } else {
        echo "<script>
            alert('Error updating product.');
            window.location.href='products.php?category_id=$category_id';
        </script>";
    }
}

// Fetch existing product details (GET)
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $product = $con->getProductById($product_id);

    if (!$product) {
        echo "<script>
            alert('Product not found.');
            window.location.href='products.php';
        </script>";
        exit();
    }
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href='products.php';
    </script>";
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body { font-family: Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .form-container { background: white; padding: 20px; border-radius: 8px; width: 400px; margin: 50px auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input[type="text"], input[type="number"] {
            width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px;
        }
        input[type="submit"] {
            background: #2980b9; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px;
        }
        input[type="submit"]:hover { background: #3498db; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Product</h2>
    <form method="POST" action="edit_product.php">
        <input type="hidden" name="products_id" value="<?= $product['products_id'] ?>">
        <input type="hidden" name="category_id" value="<?= $product['category_id'] ?>">

        <label>Product Name:</label>
        <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required>

        <label>Price:</label>
        <input type="number" step="0.01" name="product_price" value="<?= htmlspecialchars($product['product_price']) ?>" required>

        <label>Stock:</label>
        <input type="number" name="product_stock" value="<?= htmlspecialchars($product['product_stock']) ?>" required>

        <input type="submit" value="Update Product">
    </form>
</div>

</body>
</html>
