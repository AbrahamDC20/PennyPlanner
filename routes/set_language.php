<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (isset($_POST['language']) && in_array($_POST['language'], ['es', 'en', 'lt'])) {
    $_SESSION['language'] = $_POST['language']; // Guardar el idioma en la sesión
}

header('Location: ../views/settings.php'); // Redirigir de vuelta a la configuración
exit();
