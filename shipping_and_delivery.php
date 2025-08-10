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
$histories = $db->getShippingAndDeliveryHistory();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shipping & Delivery History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
    .container { margin-top: 50px; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { font-weight: 700; color: #0046af; margin-bottom: 25px; }
    table { font-size: 0.95rem; }
    th { background-color: #0046af !important; color: white; font-weight: 600; }
    tr:hover { background-color: #f1f1f1; }
    .btn-secondary { background-color: #6c757d; border: none; }
    .btn-secondary:hover { background-color: #5a6268; }
  </style>
</head>
<body>

<div class="container">
  <h2><i class="bi bi-truck"></i> Shipping & Delivery History</h2>
  <table class="table table-hover align-middle text-center">
    <thead>
      <tr>
        <th>Delivery ID</th>
        <th>Order #</th>
        <th>Placed By</th>
        <th>Order Date</th>
        <th>Tracking #</th>
        <th>Method</th>
        <th>Est. Delivery</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($histories): ?>
        <?php foreach($histories as $h): ?>
          <tr>
            <td><?= $h['delivery_id'] ?></td>
            <td>#<?= $h['order_id'] ?></td>
            <td><?= htmlspecialchars($h['username']) ?></td>
            <td><?= date('M d, Y', strtotime($h['order_date'])) ?></td>
            <td><?= htmlspecialchars($h['tracking_number']) ?></td>
            <td><?= htmlspecialchars($h['shipping_method']) ?></td>
            <td><?= date('M d, Y', strtotime($h['estimated_delivery_date'])) ?></td>
            <td>
              <span class="badge bg-<?= 
                $h['delivery_status'] === 'Pending' ? 'warning' :
                ($h['delivery_status'] === 'Shipped' ? 'primary' :
                ($h['delivery_status'] === 'Delivered' ? 'success' : 'danger')) ?>">
                <?= htmlspecialchars($h['delivery_status']) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8" class="text-muted">No shipping history found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <a href="orders.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Back to Orders</a>
</div>

</body>
</html>
