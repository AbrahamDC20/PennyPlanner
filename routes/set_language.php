<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
if (isset($_POST['language']) && in_array($_POST['language'], ['es', 'en', 'lt'])) {
    $_SESSION['language'] = $_POST['language'];
}
$redirect = $_SERVER['HTTP_REFERER'] ?? '/Website_Technologies_Abraham/Final_Proyect/views/index.php'; // Redirect to index.php if no HTTP_REFERER
header('Location: ' . $redirect);
exit();
