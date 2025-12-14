<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$id = $_GET['id'] ?? $_POST['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $id) {
    $stmt = $conn->prepare("SELECT * FROM hargalayanan WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $layanan = $stmt->get_result()->fetch_assoc();
    
    header("Content-Type: application/json");
    echo json_encode(['success' => true, 'layanan' => $layanan]);
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
    
    $stmt = $conn->prepare("UPDATE hargalayanan SET nama_layanan=?, harga=? WHERE id=?");
    $stmt->bind_param("sdi", $nama, $harga, $id);
    
    header("Content-Type: application/json");
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Layanan diperbarui']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal memperbarui layanan']);
    }
    exit;
}

header("Content-Type: application/json");
echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>
