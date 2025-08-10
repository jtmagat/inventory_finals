<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id']) ||
    ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inventory_staff')) {
    header("Location: login.php");
    exit();
}

$db = new database();
$orders = $db->getSupplierOrders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f4f6f9; padding: 30px; }
        h2 {
            color: #0046af; font-weight: 700; margin-bottom: 20px; font-size: 40px;
        }
        .btn-secondary, .btn-warning { margin-right: 10px; }
        table {
            width: 100%; border-collapse: collapse; background-color: white; border-radius: 8px; overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #0046af; color: white; font-weight: 600;
        }
        tr:hover { background-color: #f9f9f9; }
        .action-icons a {
            margin-right: 10px; font-size: 18px;
        }
        .text-success:hover, .text-danger:hover, .text-primary:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<h2>Supplier Orders</h2>
<a href="suppliers.php" class="btn btn-secondary mb-3">← Back</a>
<a href="add_supplier_order.php" class="btn btn-warning mb-3">Add New Order</a>

<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Order Date</th>
            <th>Expected Delivery</th>
            <th>Total Cost</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= htmlspecialchars($order['supplier_order_id']) ?></td>
            <td><?= htmlspecialchars($order['supplier_name']) ?></td>
            <td><?= htmlspecialchars($order['order_date']) ?></td>
            <td><?= htmlspecialchars($order['expected_delivery_date']) ?></td>
            <td>₱<?= number_format($order['total_cost'], 2) ?></td>
            <td><?= htmlspecialchars($order['order_status']) ?></td>
            <td class="action-icons">
                <a href="edit_supplier_order.php?id=<?= $order['supplier_order_id'] ?>" class="text-primary" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <a href="delete_supplier_order.php?id=<?= $order['supplier_order_id'] ?>" class="text-danger" title="Delete" onclick="return confirm('Delete this order?');">
                    <i class="bi bi-trash"></i>
                </a>
                <?php if ($order['order_status'] === 'Pending'): ?>
                <a href="confirm_order.php?id=<?= $order['supplier_order_id'] ?>" class="text-success" title="Confirm" onclick="return confirm('Confirm this order?');">
                    <i class="bi bi-check-circle"></i>
                </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (count($orders) === 0): ?>
        <tr><td colspan="7" class="text-center text-muted">No orders found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- SweetAlert for feedback -->
<?php if (isset($_GET['status'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let status = "<?= $_GET['status'] ?>";
    switch (status) {
        case 'added':
            Swal.fire('Order Added!', 'The supplier order was successfully created.', 'success');
            break;
        case 'edited':
            Swal.fire('Order Updated!', 'The supplier order was successfully updated.', 'info');
            break;
        case 'deleted':
            Swal.fire('Order Deleted!', 'The supplier order was removed.', 'error');
            break;
        case 'confirmed':
            Swal.fire('Order Confirmed!', 'The supplier order is now marked as Approved.', 'success');
            break;
    }
</script>
<?php endif; ?>

</body>
</html>
