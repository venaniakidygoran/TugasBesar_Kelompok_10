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
$status = $_POST['status'] ?? '';

if (empty($id) || empty($status)) {
    echo json_encode(['success' => false, 'error' => 'ID dan status harus diisi']);
    exit;
}

$valid_statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Status tidak valid']);
    exit;
}

$stmt = $conn->prepare("UPDATE booking SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal update status: ' . $conn->error]);
}
?>
