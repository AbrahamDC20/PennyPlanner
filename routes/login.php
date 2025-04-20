<?php
session_start();
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = getUser($username, $password);

    if ($user) {
        $_SESSION['user'] = $user; // Guardar datos del usuario en la sesión
        header('Location: ../views/index.php');
        exit();
    } else {
        $_SESSION['error'] = t('invalid_credentials'); // Mensaje de error traducido
        header('Location: ../views/login.php');
        exit();
    }
}
?>
