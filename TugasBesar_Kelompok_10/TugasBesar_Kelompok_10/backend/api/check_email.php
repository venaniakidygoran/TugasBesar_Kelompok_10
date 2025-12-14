<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email'])) {
    $email = trim($_GET['email']);
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    header("Content-Type: application/json");
    echo json_encode(['exists' => $stmt->num_rows > 0]);
    exit;
}

header("Content-Type: application/json");
echo json_encode(['error' => 'Invalid request']);
?>
