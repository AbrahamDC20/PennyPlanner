<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $friendUsername = sanitizeInput($_POST['friend_username']);
    $userId = $_SESSION['user']['id'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $friendUsername);
    $stmt->execute();
    $friend = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($friend) {
        $friendId = $friend['id'];
        $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("ii", $userId, $friendId);
        $stmt->execute();
        $stmt->close();
    }

    $redirectUrl = $_SESSION['user']['role'] === 'admin' ? '../views/admin.php' : '../views/friends.php';
    header("Location: $redirectUrl");
    exit();
}
