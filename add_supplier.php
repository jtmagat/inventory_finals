<?php
require_once('classes/database.php');
$sweetAlertConfig = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = trim($_POST['supplier_name']);
    $supplier_email = trim($_POST['supplier_email']);
    $supplier_phonenumber = trim($_POST['supplier_phonenumber']);

    // Backend validation
    if (empty($supplier_name) || empty($supplier_email) || empty($supplier_phonenumber)) {
        $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Missing Fields',
            text: 'Please fill in all fields.'
          });
        </script>";
    } elseif (!filter_var($supplier_email, FILTER_VALIDATE_EMAIL)) {
        $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.'
          });
        </script>";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $supplier_phonenumber)) {
        $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Invalid Phone Number',
            text: 'Phone number must contain only digits (10 to 15 characters).'
          });
        </script>";
    } else {
        $db = new database();
        $result = $db->addSupplier($supplier_name, $supplier_email, $supplier_phonenumber);

        if ($result) {  
            $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'success',
                title: 'Supplier Added Successfully',
                confirmButtonText: 'OK'
              }).then(() => {
                window.location.href = 'suppliers.php';
              });
            </script>";
        } else {
            $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Add Supplier Failed',
                text: 'An error occurred while adding the supplier.'
              });
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Supplier</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
  <h2>Add Supplier</h2>
  <form id="addSupplierForm" action="" method="POST">
    <div class="mb-3">
      <label for="supplier_name" class="form-label">Supplier Name</label>
      <input type="text" id="supplier_name" name="supplier_name" class="form-control" required>
      <div class="invalid-feedback">Supplier Name is required.</div>
    </div>

    <div class="mb-3">
      <label for="supplier_email" class="form-label">Email</label>
      <input type="email" id="supplier_email" name="supplier_email" class="form-control" required>
      <div class="invalid-feedback">Valid Email is required.</div>
    </div>

    <div class="mb-3">
      <label for="supplier_phonenumber" class="form-label">Phone Number</label>
      <input type="text" id="supplier_phonenumber" name="supplier_phonenumber" class="form-control" required>
      <div class="invalid-feedback">Valid Phone Number is required (10-15 digits).</div>
    </div>

    <button type="submit" id="addSupplierButton" class="btn btn-primary">Add Supplier</button>
  </form>
</div>

<script>
const supplierName = document.getElementById('supplier_name');
const supplierEmail = document.getElementById('supplier_email');
const supplierPhone = document.getElementById('supplier_phonenumber');

function validateField(field, validationFn) {
  field.addEventListener('input', () => {
    if (validationFn(field.value)) {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
    } else {
      field.classList.remove('is-valid');
      field.classList.add('is-invalid');
    }
  });
}

const isNotEmpty = (value) => value.trim() !== '';
const isValidEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
const isValidPhone = (value) => /^[0-9]{10,15}$/.test(value);

validateField(supplierName, isNotEmpty);
validateField(supplierEmail, isValidEmail);
validateField(supplierPhone, isValidPhone);

document.getElementById('addSupplierForm').addEventListener('submit', function(e) {
  let isValid = true;
  [supplierName, supplierEmail, supplierPhone].forEach((field) => {
    if (!field.classList.contains('is-valid')) {
      field.classList.add('is-invalid');
      isValid = false;
    }
  });

  if (!isValid) e.preventDefault();
});
</script>

<?php echo $sweetAlertConfig; ?>
</body>
</html>
