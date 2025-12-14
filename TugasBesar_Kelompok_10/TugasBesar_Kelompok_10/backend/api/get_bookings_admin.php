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

$stmt = $conn->prepare("
    SELECT 
        b.id, 
        b.user_id, 
        u.email,
        b.layanan_id, 
        b.tanggal, 
        b.waktu, 
        b.catatan,
        b.status, 
        l.nama_layanan
    FROM booking b 
    JOIN users u ON b.user_id = u.id
    JOIN hargalayanan l ON b.layanan_id = l.id 
    ORDER BY b.tanggal DESC, b.waktu DESC
");

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
    exit;
}

$result = $stmt->get_result();
$bookings = [];

while($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode(['success' => true, 'bookings' => $bookings]);
?>

