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

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$totalOrders = $db->getOrderCount();
$totalPages = ceil($totalOrders / $limit);

$orders = $db->getOrdersPaginated($limit, $offset);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background-color: #f4f6f9; }
    .sidebar {
        width: 240px; height: 100vh; background-color: #0046af; color: white; position: fixed; padding: 20px;
    }
    .sidebar h2 {
        margin-bottom: 30px; font-size: 1.8rem; font-weight: 700; color: #ffc107; padding-left: 10px; margin-top: 30px;
    }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar ul li { margin: 15px 0; }
    .sidebar ul li a {
        color: white; text-decoration: none; display: block; padding: 10px; border-radius: 4px; transition: background-color 0.3s;
    }
    .sidebar ul li a:hover { background-color: #003d80; }
    .has-submenu > a::after {
        content: "\25BC"; float: right; font-size: 0.7rem; transition: transform 0.3s;
    }
    .submenu { list-style: none; padding-left: 20px; display: none; }
    .has-submenu.active .submenu { display: block; }
    .has-submenu.active > a::after { transform: rotate(180deg); }
    .main-content {
      margin-left: 260px; padding: 40px 30px;
    }
    h2 { color: #0046af; font-weight: 700; margin-bottom: 10px; font-size: 50px; }
    .add-btn {
      background-color: #ffc107; padding: 10px 20px; margin-bottom: 20px; display: inline-block; color: black;
      text-decoration: none; border-radius: 4px; transition: background-color 0.3s; font-weight: 500;
    }
    .add-btn:hover { background-color: #e0a800; }
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
    .dropdown-menu {
      display: none; position: absolute; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); padding: 8px; border-radius: 6px; z-index: 10;
    }
  </style>
</head>
<body>
<?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
<script>
  document.addEventListener('DOMContentLoaded',function(){
    Swal.fire({
      icon:'success', title:'Payment Successful', text:'The payment has been recorded and the order is marked as completed.', confirmButtonColor:'#3085d6', confirmButtonText:'OK'
    });
  });
</script>
<?php endif; ?>
<div class="sidebar">
    <h2><i class="bi bi-speedometer2"></i> <?= ($_SESSION['role']==='admin') ? 'Admin Panel' : 'Inventory Panel'; ?></h2>
   <ul>
  <li><a href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'inventory_dashboard.php'; ?>"><i class="bi bi-house-door"></i> Dashboard</a></li>
  <?php if ($_SESSION['role'] === 'admin'): ?>
    <li><a href="users.php"><i class="bi bi-people"></i> Users</a></li>
  <?php endif; ?>
  <li><a href="products.php"><i class="bi bi-box"></i> Products</a></li>
  <li><a href="orders.php" class="active"><i class="bi bi-cart"></i> Orders</a></li>
  <li class="has-submenu">
    <a href="#"><i class="bi bi-receipt"></i> Sales</a>
    <ul class="submenu">
      <li><a href="add_transaction.php">Inventory Transactions</a></li>
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <li><a href="sales_report.php">Sales Report</a></li>
      <?php endif; ?>
    </ul>
  </li>
  <li class="has-submenu">
    <a href="#"><i class="bi bi-cash-coin"></i> Transactions</a>
    <ul class="submenu">
      <li><a href="payments.php">Payment and Invoicing</a></li>
      <li><a href="shipping_and_delivery.php">Shipping and Delivery</a></li>
    </ul>
  </li>
  <li><a href="suppliers.php"><i class="bi bi-truck"></i> Suppliers</a></li>
  <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
</ul>
</div>

<div class="main-content">
  <div class="container">
    <h2 class="mb-4">Orders</h2>
    <a href="create_order.php" class="btn btn-warning mb-3">Create New Order</a>
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th>Order ID</th>
                <th>Placed By</th>
                <th>Order Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if(count($orders)>0): foreach($orders as $order): ?>
              <tr>
                <td>#<?=htmlspecialchars($order['order_id'])?></td>
                <td><?=htmlspecialchars($order['username'])?></td>
                <td><?=htmlspecialchars(date('M d, Y',strtotime($order['order_date'])))?></td>
                <td>₱<?=number_format($order['total_amount'],2)?></td>
                <td><?=htmlspecialchars($order['order_status'])?></td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" onclick="toggleDropdown(this)">Options</button>
                    <div class="dropdown-menu">
                      <a href="view_order_items.php?order_id=<?= $order['order_id'] ?>" class="dropdown-item">View</a>
                      <?php if ($order['order_status'] !== 'Completed'): ?>
                        <a href="add_payment.php?order_id=<?= $order['order_id'] ?>&user_id=<?= $order['user_id'] ?>" class="dropdown-item">Pay</a>
                      <?php else: ?>
                        <span class="dropdown-item text-muted">Paid</span>
                      <?php endif; ?>
                      <button class="dropdown-item delete-order" data-id="<?= $order['order_id'] ?>">Delete</button>
                    </div>
                  </div>
                </td>
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="6" class="text-center text-muted">No orders found.</td></tr>
              <?php endif;?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav>
          <ul class="pagination justify-content-center mt-3">
            <?php if ($page > 1): ?>
              <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
              </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
              <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.has-submenu > a').forEach(link=>link.addEventListener('click',e=>{
  e.preventDefault();
  link.parentElement.classList.toggle('active');
}));

document.querySelectorAll('.delete-order').forEach(btn=>btn.addEventListener('click',function(){
  const id=this.getAttribute('data-id');
  Swal.fire({
    title:'Delete this order?', text:"It will be marked Deleted.", icon:'warning',
    showCancelButton:true, confirmButtonText:'Yes'
  }).then(r=>{ if(r.isConfirmed) window.location.href='delete_order.php?order_id='+id; });
}));

function toggleDropdown(button) {
  const menu = button.nextElementSibling;
  const isOpen = menu.style.display === 'block';
  document.querySelectorAll('.dropdown-menu').forEach(drop => drop.style.display = 'none');
  menu.style.display = isOpen ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
  if (!e.target.matches('.dropdown-toggle')) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.style.display = 'none');
  }
});
</script>
</body>
</html>
