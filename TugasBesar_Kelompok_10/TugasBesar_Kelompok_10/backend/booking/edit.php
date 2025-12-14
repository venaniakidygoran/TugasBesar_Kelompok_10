<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$uid = $_SESSION['user_id'];
$id = $_GET['id'] ?? $_POST['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $id) {
    $stmt = $conn->prepare("SELECT * FROM booking WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    header("Content-Type: application/json");
    echo json_encode(['success' => true, 'booking' => $booking]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $layanan_id = $_POST['layanan_id'] ?? '';
    $tanggal    = $_POST['tanggal'] ?? '';
    $waktu      = $_POST['waktu'] ?? '';
    $catatan    = $_POST['catatan'] ?? NULL;
    $latitude   = $_POST['latitude'] ?? NULL;
    $longitude  = $_POST['longitude'] ?? NULL;
    
    $stmt = $conn->prepare("UPDATE booking SET layanan_id=?, tanggal=?, waktu=?, catatan=?, latitude=?, longitude=? WHERE id=? AND user_id=?");
    $stmt->bind_param("isssddii", $layanan_id, $tanggal, $waktu, $catatan, $latitude, $longitude, $id, $uid);
    
    header("Content-Type: application/json");
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking diperbarui']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal memperbarui booking']);
    }
    exit;
}

header("Content-Type: application/json");
echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>
