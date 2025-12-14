<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.id, b.layanan_id, b.tanggal, b.waktu, b.catatan, b.status, b.latitude, b.longitude, b.created_at, l.nama_layanan, l.harga
    FROM booking b 
    JOIN hargalayanan l ON b.layanan_id = l.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
$bookings = [];

while($row = $res->fetch_assoc()){
    $bookings[] = $row;
}

header("Content-Type: application/json");
echo json_encode(['success' => true, 'bookings' => $bookings]);
?>
