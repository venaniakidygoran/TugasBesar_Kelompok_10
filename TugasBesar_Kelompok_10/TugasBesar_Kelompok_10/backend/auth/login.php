<?php
session_start();
require '../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'] ?? '';

    if ($email && $pass) {
        $stmt = $conn->prepare("SELECT id,nama,password,is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($user = $res->fetch_assoc()) {
            if (password_verify($pass, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['is_admin'] = $user['is_admin'];
                header("Content-Type: application/json");
                echo json_encode(['success' => true, 'message' => 'Login berhasil', 'is_admin' => $user['is_admin']]);
                exit;
            } else $error = "Email atau password salah.";
        } else{ $error = "Email atau password salah.";}
    } else $error = "Isi email dan password.";
}

header("Content-Type: application/json");
echo json_encode(['success' => false, 'error' => $error]);
?>
