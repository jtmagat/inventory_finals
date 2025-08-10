<?php
session_start();
require_once('classes/database.php');
$con = new database();
 
if (isset($_SESSION['user_id'])) {
  if ($_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit();
  } elseif ($_SESSION['role'] == 'inventory_staff') {
    header("Location: inventory_dashboard.php");
    exit();
  }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sales & Inventory</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
 
  <style>
    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #cddcfa, #e6e6fa);
      background-image: url('images/final_pic.png');
      background-size: cover;
      background-attachment: fixed;
      color: #002147;
      scroll-behavior: smooth;
    }
 
    .navbar {
      background-color: rgba(0, 123, 255, 0.6);
      backdrop-filter: blur(12px);
      padding: 10px 0;
      transition: background 0.3s ease-in-out;
    }
 
    .navbar-brand {
      font-weight: 700;
      color: gold !important;
      display: flex;
      align-items: center;
      font-size: 1.5rem;
      transition: transform 0.3s;
    }
 
    .navbar-brand:hover {
      transform: scale(1.05);
    }
 
    .navbar-brand img {
      height: 40px;
      margin-right: 10px;
    }
 
    .nav-link {
      color: white !important;
      font-weight: 500;
      padding: 8px 15px;
      border-radius: 8px;
      transition: background 0.2s, transform 0.2s;
    }
 
    .nav-link:hover {
      color: gold !important;
      background-color: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }
 
    .section-fullscreen {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 80px 20px;
    }
 
    .hero-card {
      background: rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(10px);
      padding: 3rem;
      text-align: center;
      color: white;
      animation: fadeIn 1s ease-in-out;
    }
 
    .hero-card h1 {
      font-size: 3rem;
      font-weight: 700;
      text-shadow: 2px 2px 6px rgba(0,0,0,0.3);
    }
 
    .hero-card p {
      font-size: 1.25rem;
      max-width: 700px;
      margin: 20px auto 0;
      color: #f5f5f5;
    }
 
    .about-card {
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 1rem;
      padding: 3rem;
      max-width: 900px;
      color: #002147;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
    }
 
    .about-card:hover {
      transform: translateY(-5px);
    }
 
    .about-card h2 {
      font-weight: bold;
    }
 
    .about-card p {
      font-size: 1.1rem;
      margin-bottom: 1rem;
    }
 
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
 
    @media (max-width: 768px) {
      .hero-card h1 {
        font-size: 2rem;
      }
 
      .hero-card p,
      .about-card p {
        font-size: 1rem;
      }
 
      .navbar-brand {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
 
<body>
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="images/logo.png" alt="Logo" />
        Sales & Inventory
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon text-white"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="#home">
              <i class="fas fa-house me-2"></i> Home
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="#about">
              <i class="fas fa-circle-info me-2"></i> About
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="login.php">
              <i class="fas fa-right-to-bracket me-2"></i> Login
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="registration.php">
              <i class="fas fa-user-plus me-2"></i> Register
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
 
  <section id="home" class="section-fullscreen">
    <div class="container">
      <div class="hero-card mx-auto" data-aos="fade-down" data-aos-duration="1200">
        <h1>Welcome to Sales & Inventory System</h1>
        <p>Track, manage, and grow your business efficiently with our smart inventory and sales platform.</p>
      </div>
    </div>
  </section>
 
  <section id="about" class="section-fullscreen">
    <div class="container d-flex justify-content-center align-items-center">
      <div class="about-card" data-aos="fade-up" data-aos-duration="1200">
        <h2 class="text-center mb-4">About Our System</h2>
        <p>Our Sales and Inventory System helps businesses manage products, sales, and stock effectively.</p>
        <p>Designed for both administrators and inventory staff, the platform offers role-based dashboards, alerts, and seamless access to records.</p>
        <p>It simplifies operations with features like automated inventory updates, sales tracking, and secured login authentication—empowering businesses to work smarter.</p>
      </div>
    </div>
  </section>
 
  <script src="./bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
</body>
</html>
 
 