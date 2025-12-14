<?php
session_start();
header("Content-Type: application/json");

require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Email dan password harus diisi']);
    exit;
}

$stmt = $conn->prepare("SELECT id, nama, email, password, is_admin FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Email tidak ditemukan']);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'error' => 'Password salah']);
    exit;
}

if ($user['is_admin'] != 1) {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak. Anda bukan admin.']);
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['is_admin'] = true;

echo json_encode([
    'success' => true,
    'message' => 'Login admin berhasil',
    'user' => [
        'id' => $user['id'],
        'nama' => $user['nama'],
        'email' => $user['email']
    ]
]);
?>
