<?php
require_once('classes/database.php');
$con = new database();

$sweetAlertConfig = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $firstname = trim($_POST['first_name']);
    $lastname = trim($_POST['last_name']);
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    $password = password_hash($password_raw, PASSWORD_DEFAULT);
    $created_at = date('Y-m-d H:i:s');

    // Backend Safety Checks (in case someone bypasses JS/AJAX)
    $valid_roles = ['admin', 'inventory_staff', 'customer'];
    if (!in_array($role, $valid_roles)) {
        $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Invalid Role',
            text: 'Please select a valid user role.'
          });
        </script>";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/', $password_raw)) {
        $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Invalid Password',
            text: 'Password must be at least 6 characters, include an uppercase letter, number, and special character.'
          });
        </script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.'
          });
        </script>";
    } elseif ($con->isEmailExists($email)) {
        $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Email Taken',
            text: 'This email is already registered.'
          });
        </script>";
    } elseif ($con->isUsernameExists($username)) {
        $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Username Taken',
            text: 'This username is already taken.'
          });
        </script>";
    } else {
        // All checks passed — proceed to insert
        $userID = $con->signupUser($firstname, $lastname, $username, $email, $password, $role, $created_at);

        if ($userID) {
            $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'success',
                title: 'User Added Successfully',
                confirmButtonText: 'OK'
              }).then(() => {
                window.location.href = 'users.php';
              });
            </script>";
        } else {
            $sweetAlertConfig = "
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Add User Failed',
                text: 'An error occurred while adding the user.'
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
  <title>Add User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
  <h2>Add User</h2>
  <form id="addUserForm" action="" method="POST">
    <div class="mb-3">
      <label for="first_name" class="form-label">First Name</label>
      <input type="text" id="first_name" name="first_name" class="form-control" required>
      <div class="invalid-feedback">First Name is required.</div>
    </div>

    <div class="mb-3">
      <label for="last_name" class="form-label">Last Name</label>
      <input type="text" id="last_name" name="last_name" class="form-control" required>
      <div class="invalid-feedback">Last Name is required.</div>
    </div>

    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" id="username" name="username" class="form-control" required>
      <div class="invalid-feedback">Username is required or already taken.</div>
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" id="email" name="email" class="form-control" required>
      <div class="invalid-feedback">Email is required or already taken.</div>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" id="password" name="password" class="form-control" required>
      <div class="invalid-feedback">
        Password must be at least 6 characters, contain one uppercase letter, one number, and one special character.
      </div>
    </div>

    <div class="mb-3">
      <label for="role" class="form-label">Role</label>
      <select id="role" name="role" class="form-select" required>
        <option value="">Select Role</option>
        <option value="admin">Admin</option>
        <option value="inventory_staff">Inventory Staff</option>
        <option value="customer">Customer</option>
      </select>
      <div class="invalid-feedback">Role is required.</div>
    </div>

    <button type="submit" id="addUserButton" class="btn btn-primary">Add User</button>
  </form>
</div>

<script>
// JS Validation and AJAX checks remain same as you posted — perfect already!
const firstName = document.getElementById('first_name');
const lastName = document.getElementById('last_name');
const username = document.getElementById('username');
const email = document.getElementById('email');
const password = document.getElementById('password');
const role = document.getElementById('role');

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
const isPasswordValid = (value) => /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/.test(value);

const checkUsernameAvailability = (usernameField) => {
  usernameField.addEventListener('input', () => {
    const usernameValue = usernameField.value.trim();
    if (usernameValue === '') {
      usernameField.classList.remove('is-valid');
      usernameField.classList.add('is-invalid');
      return;
    }

    fetch('ajax/check_username.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `username=${encodeURIComponent(usernameValue)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.exists) {
        usernameField.classList.remove('is-valid');
        usernameField.classList.add('is-invalid');
      } else {
        usernameField.classList.remove('is-invalid');
        usernameField.classList.add('is-valid');
      }
    });
  });
};

const checkEmailAvailability = (emailField) => {
  emailField.addEventListener('input', () => {
    const emailValue = emailField.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailValue)) {
      emailField.classList.remove('is-valid');
      emailField.classList.add('is-invalid');
      return;
    }

    fetch('ajax/check_email.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `email=${encodeURIComponent(emailValue)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.exists) {
        emailField.classList.remove('is-valid');
        emailField.classList.add('is-invalid');
      } else {
        emailField.classList.remove('is-invalid');
        emailField.classList.add('is-valid');
      }
    });
  });
};

// Attach validation
validateField(firstName, isNotEmpty);
validateField(lastName, isNotEmpty);
checkUsernameAvailability(username);
checkEmailAvailability(email);
validateField(password, isPasswordValid);
validateField(role, isNotEmpty);

document.getElementById('addUserForm').addEventListener('submit', function(e) {
  let isValid = true;
  [firstName, lastName, username, email, password, role].forEach((field) => {
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
