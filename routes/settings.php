<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['response_mode'])) {
        $_SESSION['response_mode'] = $_POST['response_mode'];
    }
    if (isset($_POST['theme'])) {
        $_SESSION['theme'] = $_POST['theme'];
    }
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '/Website_Technologies_Abraham/Final_Proyect/views/settings.php';
header('Location: ' . $redirect);
exit();
