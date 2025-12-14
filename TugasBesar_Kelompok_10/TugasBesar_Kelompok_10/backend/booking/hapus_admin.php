<?php
session_start();
header("Content-Type: application/json");

require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$admin_check = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$admin_check->bind_param("i", $_SESSION['user_id']);
$admin_check->execute();
$admin_result = $admin_check->get_result();
$admin = $admin_result->fetch_assoc();

if (!$admin || $admin['is_admin'] != 1) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

$id = $_POST['id'] ?? '';

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID booking tidak ditemukan']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM booking WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Booking berhasil dihapus']);
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal menghapus booking: ' . $conn->error]);
}
?>
