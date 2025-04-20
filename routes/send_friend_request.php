<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $friendId = intval($_POST['friend_id']);
    $userId = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $userId, $friendId);
    $stmt->execute();
    $stmt->close();

    header('Location: ../views/friends_management.php');
    exit();
}
