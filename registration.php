<?php
 
require_once('classes/database.php');
$con = new database();
 
$sweetAlertConfig = "";
if (isset($_POST['register'])){
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password_raw = $_POST['password'];
  $firstname = $_POST['first_name'];
  $lastname = $_POST['last_name'];
  $role = isset($_POST['role']) ? $_POST['role'] : '';
 
  $password = password_hash($password_raw, PASSWORD_DEFAULT);
 
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
  } else {
    // Proceed with password validation and user creation
    $passwordValid = preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/', $password_raw);
 
    if (!$passwordValid) {
      $sweetAlertConfig = "
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Invalid Password',
            text: 'Password must be at least 6 characters long, include an uppercase letter, a number, and a special character.'
          });
        </script>";
    } else {
      $created_at = date('Y-m-d H:i:s');
 
  $userID = $con->signupUser($firstname, $lastname, $username, $email, $password, $role, $created_at);
 
  if ($userID){
    $sweetAlertConfig = "
    <script>
    Swal.fire({
      icon: 'success',
      title: 'Registration Successful',
      text: 'You have successfully registered as an  $role.',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = 'login.php'
    });
    </script>";
   
  }else{
    $sweetAlertConfig = "
    <script>
    Swal.fire({
      icon: 'error',
      title: 'Registration Failed',
      text: 'An error occured during registration. Please try again.',
      confirmButtonText: 'OK'
    });
    </script>";
  }
}
}
}
 
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Registration</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="./package/dist/sweetalert2.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
  body {
    background: linear-gradient(135deg, #cddcfa, #e6e6fa);
    background-image: url('images/final_pic.png');
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
  }
  .card {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 1rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
  }
  .card-header {
    background: rgba(0, 123, 255, 0.6);
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    text-align: center;
  }
  .card-header h4 {
    font-weight: 600;
    font-size: 1.25rem;
    margin-bottom: 0;
  }
  .form-label {
    font-weight: 500;
    margin-bottom: 0.3rem;
  }
  .input-group {
    margin-bottom: 1rem;
  }
  .input-group-text {
    background-color: rgba(255, 255, 255, 0.5);
    border-right: 0;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #333;
  }
  .form-control,
  .form-select {
    background-color: rgba(255, 255, 255, 0.3);
    border-left: 0;
    border: 1px solid rgba(255, 255, 255, 0.2);
    height: 45px;
    color: #000;
    font-size: 0.95rem;
  }
  .form-control::placeholder {
    color: rgba(0, 0, 0, 0.6);
  }
  .form-select {
    height: 45px;
  }
  .invalid-feedback {
    font-size: 0.85rem;
    margin-top: 0.25rem;
  }
  .form-control.is-valid,
  .form-select.is-valid {
    border-color: #198754;
    background-color: rgba(209, 231, 221, 0.3);
  }
  .form-control.is-invalid,
  .form-select.is-invalid {
    border-color: #dc3545;
    background-color: rgba(248, 215, 218, 0.3);
  }
  button[type="submit"] {
    height: 45px;
    font-weight: 600;
    background-color: rgba(0, 123, 255, 0.8);
    border: none;
    color: white;
    transition: background-color 0.3s ease;
    border-radius: 0.375rem;
  }
  button[type="submit"]:hover {
    background-color: rgba(0, 123, 255, 1);
  }
  @media (max-width: 576px) {
    .card {
      margin: 1rem;
    }
  }
</style>
 
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow border-0">
          <div class="card-header bg-primary text-white text-center py-3 rounded-top">
            <h4 class="mb-0">📋 Registration</h4>
          </div>
          <div class="card-body">
            <form id="registrationForm" method="POST" action="" autocomplete="off" novalidate>
 
              <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person"></i></span>
                  <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter your first name" required />
                  <div class="invalid-feedback">First name is required.</div>
                </div>
              </div>
 
              <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person"></i></span>
                  <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter your last name" required />
                  <div class="invalid-feedback">Last name is required.</div>
                </div>
              </div>
 
 
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                  <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required />
                  <div class="invalid-feedback">Username is required.</div>
                </div>
              </div>

              <div class="mb-3">
                  <label for="role" class="form-label">Role</label>
                <div class="input-group">
               <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <select name="role" id="role" class="form-select" required>
              <option value="">Select a role</option>
              <option value="admin">Admin</option>
                <option value="inventory_staff">Inventory Staff</option>
                <option value="customer">Customer</option>
                </select>
            <div class="invalid-feedback">Please select a role.</div>
          </div>
        </div>

 
 
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required />
                  <div class="invalid-feedback">Email is required.</div>
                </div>
              </div>
 
 
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required />
                  <div class="invalid-feedback">
                    Password must be at least 6 characters long, include an uppercase letter, a number, and a special character.
                  </div>
                </div>
              </div>
 
 
              <button id="registerButton" type="submit" name="register" class="btn btn-primary w-100" disabled>
                Register Account
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
 
 
  <script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
  <script src="./package/dist/sweetalert2.js"></script>
  <?php echo $sweetAlertConfig?>
 
  <script>
 
  // Function to validate individual fields
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
 
  // Validation functions for each field
  const isNotEmpty = (value) => (value).trim() !== '';
  const isPasswordValid = (value) => {
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
    return passwordRegex.test(value);
  };
  const isEmailValid = (value) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(value);
};
 
 
  // Real-time username validation using AJAX
  const checkUsernameAvailability = (usernameField) => {
    usernameField.addEventListener('input', () => {
      const username = usernameField.value.trim();
 
      if (username === '') {
        usernameField.classList.remove('is-valid');
        usernameField.classList.add('is-invalid');
        usernameField.nextElementSibling.textContent = 'Username is required.';
        registerButton.disabled = true; //disabled the button
        return;
      }
 
      // Send AJAX request to check username availability
      fetch('ajax/check_username.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `username=${encodeURIComponent(username)}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.exists) {
            usernameField.classList.remove('is-valid');
            usernameField.classList.add('is-invalid');
            usernameField.nextElementSibling.textContent = 'Username is already taken.';
            registerButton.disabled = true; //disabled the button
          } else {
            usernameField.classList.remove('is-invalid');
            usernameField.classList.add('is-valid');
            usernameField.nextElementSibling.textContent = '';
            registerButton.disabled = false; //disabled the button
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          registerButton.disabled = true; //disabled the button
        });
    });
  };
 
  const checkEmailAvailability = (emailField) => {
  emailField.addEventListener('input', () => {
    const email = emailField.value.trim();
 
    if (email === '') {
      emailField.classList.remove('is-valid');
      emailField.classList.add('is-invalid');
      emailField.nextElementSibling.textContent = 'Email is required.';
      registerButton.disabled = true;
      return;
    }
 
    if (!isEmailValid(email)) {
      emailField.classList.remove('is-valid');
      emailField.classList.add('is-invalid');
      emailField.nextElementSibling.textContent = 'Please enter a valid email format.';
      registerButton.disabled = true;
      return;
    }
 
    // Format is valid, now check availability
    fetch('ajax/check_email.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `email=${encodeURIComponent(email)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.exists) {
          emailField.classList.remove('is-valid');
          emailField.classList.add('is-invalid');
          emailField.nextElementSibling.textContent = 'Email is already taken.';
          registerButton.disabled = true;
        } else {
          emailField.classList.remove('is-invalid');
          emailField.classList.add('is-valid');
          emailField.nextElementSibling.textContent = '';
          registerButton.disabled = false;
        }
      })
      .catch((error) => {
        console.error('Error:', error);
        registerButton.disabled = true;
      });
  });
};
 
 
  // Get form fields
  const firstName = document.getElementById('first_name');
  const lastName = document.getElementById('last_name');
  const username = document.getElementById('username');
  const email = document.getElementById('email');
  const password = document.getElementById('password');
 
  // Attach real-time validation to each field
  validateField(firstName, isNotEmpty);
  validateField(lastName, isNotEmpty);
  checkUsernameAvailability(username);
  checkEmailAvailability(email);
  validateField(password, isPasswordValid);
 
  // Form submission validation
  document.getElementById('registrationForm').addEventListener('submit', function (e) {
    //e.preventDefault(); // Prevent form submission for validation
 
    let isValid = true;
 
    // Validate all fields on submit
    [firstName, lastName, username, email, password].forEach((field) => {
      if (!field.classList.contains('is-valid')) {
        field.classList.add('is-invalid');
        isValid = false;
      }
    });
 
    // If all fields are valid, submit the form
    if (isValid) {
      this.submit();
    }
  });
</script>
 
 
</body>
</html>
 