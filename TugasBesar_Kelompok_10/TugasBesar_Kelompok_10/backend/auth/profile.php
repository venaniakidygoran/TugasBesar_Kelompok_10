<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

require '../config/database.php';
$uid = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT nama,email,no_hp,foto,is_admin FROM users WHERE id=?");
    $stmt->bind_param("i",$uid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    header("Content-Type: application/json");
    echo json_encode(['success' => true, 'user' => $user]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nama = $_POST['nama'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';

    if (!empty($_FILES['foto']['name'])) {
        $target_dir = '../uploads/profile/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = 'user_'.$uid.'_'.time().'.'.$ext;
        $target = $target_dir.$filename;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $stmt = $conn->prepare("UPDATE users SET nama=?, no_hp=?, foto=? WHERE id=?");
            $stmt->bind_param("sssi",$nama,$no_hp,$filename,$uid);
            $stmt->execute();
            $msg = "Profil diperbarui.";
        } else $msg = "Gagal meng-upload foto.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama=?, no_hp=? WHERE id=?");
        $stmt->bind_param("ssi",$nama,$no_hp,$uid);
        $stmt->execute();
        $msg = "Profil diperbarui.";
    }
    
    header("Content-Type: application/json");
    echo json_encode(['success' => true, 'message' => $msg]);
    exit;
}

header("Content-Type: application/json");
echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>
