<?php
session_start();
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php'; // Add this line

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = getUser($username, $password);

    if ($user) {
        if ($user['role'] !== 'admin') {
            $_SESSION['error'] = t('user_login_only'); // Mensaje de error traducido
            header('Location: ../views/admin_login.php');
            exit();
        }
        $_SESSION['user'] = $user; // Guardar datos del administrador en la sesión
        header('Location: ../views/admin.php');
        exit();
    } else {
        $_SESSION['error'] = t('invalid_credentials'); // Mensaje de error traducido
        header('Location: ../views/admin_login.php');
        exit();
    }
}
?>
