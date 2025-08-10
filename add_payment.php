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

$order_id = $_GET['order_id'] ?? '';
$user_id = $_GET['user_id'] ?? '';
$success = '';
$error = '';
$order_items = [];
$total_amount = 0;

if (!empty($order_id)) {
    $order_items = $db->getOrderItems($order_id);
    $orderDetails = $db->getOrderDetailsById($order_id); 

    if ($orderDetails) {
        $total_amount = $orderDetails['total_amount']; 
    } else {
        $error = "Order details not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $user_id = $_POST['user_id'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount_paid'];
    $tracking_number = $_POST['tracking_number'] ?? '';
    $shipping_method = $_POST['shipping_method'] ?? '';
    $estimated_delivery_date = $_POST['estimated_delivery_date'] ?? '';
    $delivery_status = $_POST['delivery_status'] ?? '';

    if (empty($order_id) || empty($user_id) || empty($payment_method) || empty($amount) ||
        empty($tracking_number) || empty($shipping_method) || empty($estimated_delivery_date) || empty($delivery_status)) {
        $error = "Please fill out all fields.";
    } else {
        try {
            $con = $db->opencon();
            $con->beginTransaction();

            $db->addPayment($order_id, $user_id, $payment_method, $amount);
            $db->addShippingDelivery($order_id, $user_id, $tracking_number, $shipping_method, $estimated_delivery_date, $delivery_status);
            $db->updateOrderStatus($order_id, 'Completed');

            $con->commit();
            $success = "Payment and delivery successfully recorded.";
        } catch (PDOException $e) {
            $con->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payment</title>
    <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 0;
        }

        .main-content {
            width: 100%;
            max-width: 800px;
            padding: 20px;
        }

        h1 {
            color: rgb(0, 70, 175);
            margin-bottom: 20px;
            font-weight: 700;
            text-align: center;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background-color: #0046af;
            border: none;
        }

        .btn-primary:hover {
            background-color: #003b96;
        }

        .btn-secondary {
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h1>Add Payment</h1>

    <div class="form-container">
<?php if ($success): ?>
    <script>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            Swal.fire({
                title: 'Success!',
                text: '<?= $success ?>',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Go to Sales Report',
                cancelButtonText: 'Back to Orders'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'sales_report.php';
                } else {
                    window.location.href = 'orders.php';
                }
            });
        <?php else: ?>
            Swal.fire({
                title: 'Success!',
                text: '<?= $success ?>',
                icon: 'success',
                confirmButtonText: 'Back to Orders'
            }).then((result) => {
                window.location.href = 'orders.php';
            });
        <?php endif; ?>
    </script>
<?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>


        <form method="POST" action="">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

            <div class="mb-3">
                <label class="form-label">Order ID</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($order_id) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">User ID</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user_id) ?>" readonly>
            </div>

            <?php if ($order_items): ?>
                <h5 class="mt-4 mb-3">Order Details</h5>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= isset($item['quantity']) ? htmlspecialchars($item['quantity']) : '0' ?></td>
                                <td>₱<?= isset($item['price']) ? number_format($item['price'], 2) : '0.00' ?></td>
                                <td>₱<?= isset($item['subtotal']) ? number_format($item['subtotal'], 2) : '0.00' ?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">No order items found for this order.</div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <select class="form-select" name="payment_method" required>
                    <option value="" disabled selected>Select method</option>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount Paid</label>
                <input type="number" name="amount_paid" class="form-control" value="<?= number_format($total_amount, 2, '.', '') ?>" readonly>
            </div>

            <!-- Delivery Section -->
            <div class="mb-3">
                <label class="form-label">Tracking Number</label>
                <input type="text" name="tracking_number" class="form-control" value="<?= htmlspecialchars(uniqid('TRK')) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Shipping Method</label>
                <select name="shipping_method" class="form-select" required>
                    <option value="" disabled selected>Select method</option>
                    <option value="Standard">Standard</option>
                    <option value="Express">Express</option>
                    <option value="Pickup">Pickup</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Estimated Delivery Date</label>
                <input type="date" name="estimated_delivery_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Delivery Status</label>
                <select name="delivery_status" class="form-select" required>
                    <option value="Not Shipped">Not Shipped</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Submit Payment</button>
            <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
        </form>
    </div>
</div>

</body>
</html>
