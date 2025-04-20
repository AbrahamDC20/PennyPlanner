<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';
require_once '../controllers/profileController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user']['id'];
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $phone, $userId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['phone'] = $phone;

    if (!empty($_FILES['profile_image']['name'])) {
        try {
            updateProfileImage($userId, $_FILES['profile_image']);
            $_SESSION['user']['profile_image'] = $_FILES['profile_image']['name'];
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }

    header('Location: ../views/profile.php');
    exit();
}
