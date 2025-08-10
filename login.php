<?php
session_start();
require_once('classes/database.php');
$con = new database();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit();
      } elseif ($_SESSION['role'] === 'inventory_staff') {
        header("Location: inventory_dashboard.php");
      } elseif ($_SESSION['role'] === 'customer') {
        header("Location: customer_dashboard.php");
        exit();
    }
}

$sweetAlertConfig = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Call method in database.php
    $user = $con->loginUser($username, $password);

    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $user['role'];

        $sweetAlertConfig = "
      <script src='./package/dist/sweetalert2.js'></script>
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Login Successful',
          text: 'Welcome, " . addslashes(htmlspecialchars($user['first_name'])) . "',
          confirmButtonText: 'Continue'
        }).then(() => {";
          if($user['role'] == 'admin'){
          $sweetAlertConfig .= "window.location.href = 'admin_dashboard.php';";
          } elseif($user['role'] == 'inventory_staff'){
          $sweetAlertConfig .= "window.location.href = 'inventory_dashboard.php';";
          } elseif($user['role'] == 'customer'){
          $sweetAlertConfig .= "window.location.href = 'customer_dashboard.php';";
         }else{
          $sweetAlertConfig .="window.location.href = 'login.php';";
        }
        $sweetAlertConfig .= "
        });
      </script>";
  } else {
    $sweetAlertConfig = "
      <script src='./package/dist/sweetalert2.js'></script>
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Login Failed',
          text: 'Invalid username or password.'
        });
      </script>";
  }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="./package/dist/sweetalert2.css" />
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
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  overflow: hidden;
  border: none;
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
  margin-bottom: 0;
  color: white;
}
 
.form-control,
.input-group-text {
  background-color: rgba(255, 255, 255, 0.3);
  border: none;  /* removed border */
  color: #000;
  box-shadow: none; /* remove any shadow */
}
 
.form-control::placeholder {
  color: rgba(0, 0, 0, 0.6);
}
 
/* Validation styles with glowing effect instead of borders */
.form-control.is-valid {
  border-color: transparent;
  background-color: rgba(209, 231, 221, 0.3);
  box-shadow: 0 0 0 2px #198754; /* green glow */
}
 
.form-control.is-invalid {
  border-color: transparent;
  background-color: rgba(248, 215, 218, 0.3);
  box-shadow: 0 0 0 2px #dc3545; /* red glow */
}
 
.invalid-feedback {
  font-size: 0.85rem;
}
 
button[type="submit"] {
  height: 45px;
  font-weight: 600;
  background-color: rgba(0, 123, 255, 0.8);
  border: none;
  color: white;
  transition: background-color 0.3s ease;
}
 
button[type="submit"]:hover {
  background-color: rgba(0, 123, 255, 1);
}
</style>
 
<body class="d-flex align-items-center justify-content-center min-vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card">
          <div class="card-header py-3 text-white">
            <h4>🔐 Login</h4>
          </div>
          <div class="card-body">
            <form method="POST" id="loginForm" novalidate>
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                  <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required>
                  <div class="invalid-feedback">This field is required.</div>
                </div>
              </div>
 
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                  <div class="invalid-feedback">Password is required.</div>
                </div>
              </div>
 
              <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-end mt-2">
              <a href="forgot_password.php" class="text-decoration-none">Forgot password?</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 
 
 
  <script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
  <script src="./package/dist/sweetalert2.js"></script>
 
  <?php
    if (isset($sweetAlertConfig)) {
      echo $sweetAlertConfig;
    }
  ?>

  <script>

  (function () {
    const form = document.getElementById('loginForm');
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  })();
</script>
</body>
</html>
 
 
 