<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars($_POST['message']);

    if ($email && $message) {
        // Aquí puedes implementar el envío de correo o guardar la solicitud en la base de datos
        $_SESSION['success'] = t('support_request_sent');
    } else {
        $_SESSION['error'] = t('invalid_input');
    }

    header('Location: ../views/customer_support.php');
    exit();
}
?>
