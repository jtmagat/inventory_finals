<?php
require_once('classes/database.php');
session_start();

$con = new database();
$sweetAlertConfig = "";

if (!isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("No supplier ID provided.");
}

$supplier_id = $_GET['id'] ?? $_POST['supplier_id'];
$supplier = $con->getSupplierById($supplier_id);

if (!$supplier) {
    die("Supplier not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = trim($_POST['supplier_name']);
    $supplier_email = trim($_POST['supplier_email']);
    $supplier_phonenumber = trim($_POST['supplier_phonenumber']);

    if (empty($supplier_name) || empty($supplier_email) || empty($supplier_phonenumber)) {
        $sweetAlertConfig = "
        <script>
            Swal.fire('Error', 'Please fill all fields.', 'error');
        </script>";
    } elseif (!filter_var($supplier_email, FILTER_VALIDATE_EMAIL)) {
        $sweetAlertConfig = "
        <script>
            Swal.fire('Error', 'Invalid email format.', 'error');
        </script>";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $supplier_phonenumber)) {
        $sweetAlertConfig = "
        <script>
            Swal.fire('Error', 'Phone number must be 10 to 15 digits.', 'error');
        </script>";
    } else {

        if ($con->updateSupplier($supplier_id, $supplier_name, $supplier_phonenumber, $supplier_email)) {
            $sweetAlertConfig = "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Supplier Updated Successfully',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'suppliers.php';
                });
            </script>";
        } else {
            $sweetAlertConfig = "
            <script>
                Swal.fire('Error', 'Failed to update supplier.', 'error');
            </script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .form-container { max-width: 600px; margin: 60px auto; }
        .card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .card-header { background-color: #0046af; color: white; font-weight: 600; font-size: 20px; text-align: center; border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .btn-primary { background-color: #0046af; border: none; }
        .btn-primary:hover { background-color: #003b91; }
        .btn-secondary { background-color:red; border: none; }
        .btn-secondary:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="container form-container">
    <div class="card">
        <div class="card-header">Edit Supplier</div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="supplier_id" value="<?= htmlspecialchars($supplier['supplier_id']) ?>">
                <div class="mb-3">
                    <label class="form-label">Supplier Name</label>
                    <input type="text" class="form-control" name="supplier_name" value="<?= htmlspecialchars($supplier['supplier_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="supplier_email" value="<?= htmlspecialchars($supplier['supplier_email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="supplier_phonenumber" value="<?= htmlspecialchars($supplier['supplier_phonenumber']) ?>" required>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="suppliers.php" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $sweetAlertConfig ?>
</body>
</html>
