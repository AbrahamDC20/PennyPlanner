<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';
require_once '../controllers/notificationsController.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notificationId = intval($_POST['notification_id']);
    markNotificationAsRead($notificationId);
    header('Location: ../views/notifications.php');
    exit();
}
?>
