<?php
require_once('classes/database.php');
$db = new database();
$sweetAlertConfig = "";

if (!isset($_GET['id'])) {
    die("No supplier ID provided.");
}

$supplier_id = $_GET['id'];
$supplier = $db->getSupplierById($supplier_id);

if (!$supplier) {
    die("Supplier not found.");
}

if ($db->deleteSupplierById($supplier_id)) {
    $sweetAlertConfig = "
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Supplier Deleted Successfully',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'suppliers.php';
        });
    </script>";
} else {
    $sweetAlertConfig = "
    <script>
        Swal.fire('Error', 'Failed to delete supplier.', 'error');
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Supplier</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?= $sweetAlertConfig ?>
</body>
</html>
