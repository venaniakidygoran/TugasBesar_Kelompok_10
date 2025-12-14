<?php
header("Content-Type: application/json");
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT id, nama_cabang, alamat, no_telepon FROM cabang ORDER BY id ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $cabang = [];
    while ($row = $result->fetch_assoc()) {
        $cabang[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $cabang]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
