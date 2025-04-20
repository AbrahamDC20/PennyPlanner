<?php
session_start();
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $_SESSION['error'] = t('invalid_csrf_token');
        header('Location: ../views/login.php');
        exit();
    }

    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = getUser($username, $password);

    if ($user) {
        if ($user['role'] === 'admin') {
            $_SESSION['error'] = t('admin_login_only'); // Mensaje de error traducido
            header('Location: ../views/login.php');
            exit();
        }
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
