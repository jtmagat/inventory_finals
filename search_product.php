<?php
require_once 'classes/database.php';
header('Content-Type: application/json');

if (!isset($_GET['q']) || trim($_GET['q']) === '') {
    echo json_encode([]);
    exit;
}

$q = trim($_GET['q']);

$db = new database();
$results = $db->searchProductByName($q);

echo json_encode($results);
?>
