<?php
require_once 'classes/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No user ID provided.");
}

$user_id = $_GET['id'];

$db = new database();
$user = $db->getUserById($user_id);

if (!$user) {
    die("User not found.");
}

if ($db->deleteUser($user_id)) {
    header("Location: users.php");
    exit();
} else {
    echo "Error deleting user.";
}
?>
