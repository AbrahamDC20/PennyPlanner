<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generar token CSRF si no existe
    }

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
        $input2FACode = $_POST['2fa_code'] ?? '';
        if (!verify2FACode($user['id'], $input2FACode)) {
            $_SESSION['error'] = t('invalid_2fa_code');
            header('Location: ../views/login.php');
            exit();
        }
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
