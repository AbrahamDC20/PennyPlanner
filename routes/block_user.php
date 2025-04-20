<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
require_once '../models/db.php';
require_once '../controllers/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blockedUserId = intval($_POST['user_id']);
    $userId = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO blocked_users (user_id, blocked_user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $blockedUserId);
    $stmt->execute();
    $stmt->close();

    header('Location: ../views/friends_management.php');
    exit();
}
