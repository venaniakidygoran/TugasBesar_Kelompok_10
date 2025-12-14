<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$uid        = $_SESSION['user_id'];
$cabang_id  = $_POST['cabang_id'] ?? '';
$layanan_id = $_POST['layanan_id'] ?? '';
$tanggal    = $_POST['tanggal'] ?? '';
$waktu      = $_POST['waktu'] ?? '';
$catatan    = !empty($_POST['catatan']) ? $_POST['catatan'] : NULL;
$status     = "pending";

if (!$cabang_id || !$layanan_id || !$tanggal || !$waktu) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Data tidak lengkap']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO booking (user_id, cabang_id, layanan_id, tanggal, waktu, catatan, status)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("iisssss", $uid, $cabang_id, $layanan_id, $tanggal, $waktu, $catatan, $status);

header("Content-Type: application/json");
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Booking berhasil']);
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal melakukan booking']);
}
?>
