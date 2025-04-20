<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['response_mode'])) {
        $_SESSION['response_mode'] = $_POST['response_mode'];
    }
    $_SESSION['notifications'] = isset($_POST['notifications']) ? 'enabled' : 'disabled';
    $_SESSION['user']['2fa_enabled'] = isset($_POST['2fa_enabled']) ? true : false;
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '/Website_Technologies_Abraham/Final_Proyect/views/settings.php';
header('Location: ' . $redirect);
exit();
