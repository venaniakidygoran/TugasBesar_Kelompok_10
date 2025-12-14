<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$res = $conn->query("SELECT * FROM hargalayanan ORDER BY id DESC");
$layanan = [];

while($r = $res->fetch_assoc()){
    $layanan[] = $r;
}

header("Content-Type: application/json");
echo json_encode(['success' => true, 'layanan' => $layanan]);
?>
