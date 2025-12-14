<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$uid = $_SESSION['user_id'];
$id = $_POST['id'] ?? '';

if (!$id) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'ID booking tidak ditemukan']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM booking WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $uid);

header("Content-Type: application/json");
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Booking dihapus']);
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal menghapus booking']);
}
?>
