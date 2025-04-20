<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $friendId = intval($_POST['friend_id']);
    $userId = $_SESSION['user']['id'];

    $stmt = $conn->prepare("DELETE FROM friends WHERE user_id = ? AND friend_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $userId, $friendId);
    $stmt->execute();
    $stmt->close();

    header('Location: ../views/friends_management.php');
    exit();
}
