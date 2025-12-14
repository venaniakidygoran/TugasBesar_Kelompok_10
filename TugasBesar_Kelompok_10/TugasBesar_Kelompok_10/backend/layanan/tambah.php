<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_layanan'] ?? '';
    $harga = $_POST['harga'] ?? '';
    
    if (!$nama || !$harga) {
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'error' => 'Data tidak lengkap']);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO hargalayanan (nama_layanan, harga) VALUES (?, ?)");
    $stmt->bind_param("sd", $nama, $harga);
    
    header("Content-Type: application/json");
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Layanan ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal menambahkan layanan']);
    }
    exit;
}

header("Content-Type: application/json");
echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>
