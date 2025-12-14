<?php
require '../config/database.php';

$res = $conn->query("SELECT * FROM hargalayanan ORDER BY id DESC");
$layanan = [];

while($r = $res->fetch_assoc()){
    $layanan[] = $r;
}

header("Content-Type: application/json");
echo json_encode(['success' => true, 'layanan' => $layanan]);
?>
