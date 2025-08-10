<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Allow both Admin and Inventory Staff
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff') {
    header("Location: login.php");
    exit();
}

$db = new database();

$categories = $db->getAllCategories();

$selectedCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

$sweetAlertConfig = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['product_name']);
    $categoryId = $_POST['category_id'];
    $productPrice = trim($_POST['product_price']);
    $productStock = trim($_POST['product_stock']);

    if (empty($productName) || empty($categoryId) || empty($productPrice) || empty($productStock)) {
        $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all fields.'
              });
            </script>";
    } else {
        $result = $db->addProduct($productName, $categoryId, $productPrice, $productStock);

        if ($result) {
            $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'success',
                title: 'Product Added',
                confirmButtonText: 'OK'
              }).then(() => {
                window.location.href = 'products.php';
              });
            </script>";
        } else {
            $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to add product.'
              });
            </script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5" style="max-width: 600px;">
  <h2>Add Product</h2>
  <form method="POST" action="">
    <div class="mb-3">
      <label for="product_name" class="form-label">Product Name</label>
      <input type="text" class="form-control" id="product_name" name="product_name" required />
    </div>

    <div class="mb-3">
      <label for="category_id" class="form-label">Category</label>
      <?php if ($selectedCategoryId > 0):
          // find category name
          $fixedCategoryName = '';
          foreach ($categories as $cat) {
              if ($cat['category_id'] == $selectedCategoryId) {
                  $fixedCategoryName = $cat['category_name'];
                  break;
              }
          }
      ?>
          <input type="text" class="form-control" value="<?= htmlspecialchars($fixedCategoryName) ?>" disabled />
          <input type="hidden" name="category_id" value="<?= htmlspecialchars($selectedCategoryId) ?>" />
      <?php else: ?>
          <select class="form-select" id="category_id" name="category_id" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>">
                    <?= htmlspecialchars($cat['category_name']) ?>
                </option>
            <?php endforeach; ?>
          </select>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label for="product_price" class="form-label">Price</label>
      <input type="number" step="0.01" class="form-control" id="product_price" name="product_price" required />
    </div>

    <div class="mb-3">
      <label for="product_stock" class="form-label">Stock</label>
      <input type="number" class="form-control" id="product_stock" name="product_stock" required />
    </div>

    <button type="submit" class="btn btn-primary">Add Product</button>
    <a href="products.php" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>

<?= $sweetAlertConfig ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
