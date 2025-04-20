<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
require_once '../models/db.php';
require_once '../controllers/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user']['id'];
    $enable2FA = isset($_POST['2fa_enabled']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE users SET 2fa_enabled = ? WHERE id = ?");
    $stmt->bind_param("ii", $enable2FA, $userId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['user']['2fa_enabled'] = $enable2FA;
    header('Location: ../views/settings.php');
    exit();
}
