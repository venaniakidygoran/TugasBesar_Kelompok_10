<?php
session_start();
require '../config/database.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $nohp = trim($_POST['no_hp']);
    $p = $_POST['password'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    if (!$nama || !$email || !$p) $errors[] = "Nama, email, dan password wajib diisi.";
    if ($p !== $p2) $errors[] = "Password dan konfirmasi tidak sama.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($p, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nama,email,no_hp,password) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $nama, $email, $nohp, $hash);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Registrasi berhasil. Silakan login.";
                header("Content-Type: application/json");
                echo json_encode(['success' => true, 'message' => 'Registrasi berhasil']);
                exit;
            } else {
                $errors[] = "Gagal menyimpan data: " . $conn->error;
            }
        }
    }
}

header("Content-Type: application/json");
echo json_encode(['success' => false, 'errors' => $errors]);
?>
