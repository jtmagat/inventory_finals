<?php
require_once('classes/database.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new database();

// Fetch sales data from the database class
$salesData = $db->getSalesReportData();

// Handle filter (Paid/Unpaid)
$filter = $_GET['filter'] ?? '';

$filtered_orders = array_filter($salesData, function($order) use ($filter) {
    return $filter === '' || $order['payment_status'] === $filter;
});
$filtered_orders = array_values($filtered_orders);

foreach ($filtered_orders as &$order) {
    $paymentInfo = $db->getTotalPaidByOrderId($order['order_id']);
    $totalPaid = $paymentInfo['total_paid'] ?? 0;

    $total_paid = isset($row['total_paid']) ? $row['total_paid'] : '0.00';
$payment_status = isset($row['payment_status']) ? $row['payment_status'] : 'Not Paid';

}
unset($order); // break reference to avoid accidental overwrite

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { background-color: #f4f6f9; font-family: 'Poppins', sans-serif; padding: 40px; }
    h2 { text-align: center; color: #0046af; margin-bottom: 30px; font-size: 36px; font-weight: 700; }
    .filter-form { margin-bottom: 25px; display: flex; gap: 10px; align-items: center; }
    .card { background-color: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-top: 5px solid #ffc107; transition: transform 0.2s; }
    .card:hover { transform: translateY(-4px); }
    .card h5 { font-size: 20px; color: #0046af; font-weight: 600; }
    .card p { margin-bottom: 5px; font-size: 14px; color: #444; }
    .back-btn { display: inline-block; margin-bottom: 30px; background-color: #0046af; color: white; padding: 10px 18px; border-radius: 6px; font-size: 14px; text-decoration: none; }
    .back-btn:hover { background-color: #003d80; color: #ffc107; }
    .order-table th { background-color: #0046af; color: white; font-size: 14px; }
    .order-table td { font-size: 13px; }
    .no-orders { text-align: center; color: #555; margin-top: 50px; font-size: 18px; }
  </style>
</head>
<body>

<a class="back-btn" href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'inventory_dashboard.php'; ?>">
  ← Back to Dashboard
</a>

<h2>Sales Report</h2>

<form method="get" class="filter-form">
  <label for="filter" style="font-weight: 600;">Filter by Payment Status:</label>
  <select name="filter" id="filter" onchange="this.form.submit()" class="form-select" style="width: 200px;">
    <option value="">All</option>
    <option value="Paid" <?= (isset($_GET['filter']) && $_GET['filter'] === 'Paid') ? 'selected' : '' ?>>Paid</option>
    <option value="Unpaid" <?= (isset($_GET['filter']) && $_GET['filter'] === 'Unpaid') ? 'selected' : '' ?>>Unpaid</option>
  </select>
</form>

<div class="container">
  <div class="row">
    <?php if (count($filtered_orders) > 0): ?>
      <?php foreach ($filtered_orders as $order): ?>
        <?php $items = $db->getOrderItemsForReport($order['order_id']); ?>
        <div class="col-md-6 col-lg-4">
          <div class="card">
            <h5>Order #<?= $order['order_id'] ?></h5>
            <p><strong>Date:</strong> <?= $order['order_date'] ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status'] ?? 'N/A') ?></p>
            <p><strong>Total Amount:</strong> ﷼<?= number_format($order['total_amount'], 2) ?></p>
            <p><strong>Total Paid:</strong> ﷼<?= number_format($order['total_paid'], 2) ?></p>
            <p><strong>Payment Status:</strong>
              <?= ($order['payment_status'] === 'Paid') ? '<span style="color: green; font-weight: 600;">Paid</span>' : '<span style="color: red; font-weight: 600;">Unpaid</span>'; ?>
            </p>
            <a href="invoice.php?order_id=<?= $order['order_id'] ?>" class="btn btn-warning btn-sm mt-2" target="_blank">
              <i class="bi bi-printer"></i> Print Invoice
            </a>

            <table class="table table-bordered order-table mt-3">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Price</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['order_quantity'] ?></td>
                    <td>﷼<?= number_format($item['order_price'], 2) ?></td>
                    <td>﷼<?= number_format($item['order_quantity'] * $item['order_price'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="no-orders">No orders found.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
