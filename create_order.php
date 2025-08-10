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

$db = new database();
$products = $db->getAvailableProducts();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Create Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
 
        body {
            background-color: #ecf0f1;
            padding: 40px;
            margin: 0;
        }
 
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
 
        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
 
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
 
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }
 
        th {
            background-color: #0046af;
            color: white;
        }
 
        input[type="number"] {
            width: 60px;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
 
        .submit-btn {
            background-color: #27ae60;
            color: white;
            padding: 12px 20px;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
 
        .submit-btn:hover {
            background-color: #219150;
        }
 
        h3 {
            text-align: right;
            color: #2c3e50;
            margin-top: 10px;
        }
 
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
 
            th {
                display: none;
            }
 
            td {
                position: relative;
                padding-left: 50%;
                text-align: left;
                border: none;
                border-bottom: 1px solid #ddd;
            }
 
            td:before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: bold;
                color: #0046af;
            }
 
            .submit-btn {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
 
<div class="container">
    <h2>Create New Order</h2>
 
    <form action="process_order.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Product</th>
                    <th>Price (﷼)</th>
                    <th>Quantity</th>
                    <th>Subtotal (﷼)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td data-label="Select">
                        <input type="checkbox" name="products[]" value="<?= $product['products_id'] ?>" class="product-check" data-price="<?= $product['product_price'] ?>">
                    </td>
                    <td data-label="Product"><?= htmlspecialchars($product['product_name']) ?></td>
                    <td data-label="Price"><?= number_format($product['product_price'], 2) ?></td>
                    <td data-label="Quantity">
                        <input type="number" name="quantities[<?= $product['products_id'] ?>]" min="1" value="1" class="quantity-input" disabled>
                    </td>
                    <td data-label="Subtotal"><span class="subtotal">0.00</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
 
        <h3>Total Amount: ﷼<span id="totalAmount">0.00</span></h3>
 
       <div style="display: flex; gap: 10px;">
    <button type="submit" class="submit-btn" style="flex: 1;">Place Order</button>
    <a href="inventory_dashboard.php" class="submit-btn" style="flex: 1; text-align: center; text-decoration: none; background-color: #e74c3c;">
        Cancel
    </a>
</div>
 
    </form>
</div>
 
<script>
    const checkboxes = document.querySelectorAll('.product-check');
    const totalAmountDisplay = document.getElementById('totalAmount');
 
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const row = this.closest('tr');
            const qtyInput = row.querySelector('.quantity-input');
            const subtotal = row.querySelector('.subtotal');
            const price = parseFloat(this.getAttribute('data-price'));
 
            if (this.checked) {
                qtyInput.disabled = false;
                subtotal.textContent = (qtyInput.value * price).toFixed(2);
            } else {
                qtyInput.disabled = true;
                subtotal.textContent = "0.00";
                qtyInput.value = 1;
            }
 
            updateTotal();
        });
    });
 
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function () {
            const row = this.closest('tr');
            const checkbox = row.querySelector('.product-check');
            const subtotal = row.querySelector('.subtotal');
            const price = parseFloat(checkbox.getAttribute('data-price'));
            if (checkbox.checked) {
                subtotal.textContent = (this.value * price).toFixed(2);
                updateTotal();
            }
        });
    });
 
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(span => {
            total += parseFloat(span.textContent);
        });
        totalAmountDisplay.textContent = total.toFixed(2);
    }
</script>
 
</body>
</html>
 
