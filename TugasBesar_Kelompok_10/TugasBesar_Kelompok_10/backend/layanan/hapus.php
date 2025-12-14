<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$id = $_POST['id'] ?? '';

if (!$id) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'ID layanan tidak ditemukan']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM hargalayanan WHERE id=?");
$stmt->bind_param("i", $id);

header("Content-Type: application/json");
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Layanan dihapus']);
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal menghapus layanan']);
}
?>
