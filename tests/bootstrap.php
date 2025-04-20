<?php
require_once __DIR__ . '/../models/db.php'; // Cambiar a ruta absoluta

// Inicializar la conexión a la base de datos para pruebas
global $conn;
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    file_put_contents(__DIR__ . '/../logs/error.log', 'Database connection failed: ' . $conn->connect_error . PHP_EOL, FILE_APPEND);
    die('Database connection failed: ' . $conn->connect_error);
}

// Manejar excepciones globalmente
set_exception_handler(function ($e) {
    file_put_contents(__DIR__ . '/../logs/error.log', 'Uncaught exception: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    throw $e;
});

try {
    ensureAdminAccount(); // Asegurarse de que la cuenta de administrador exista
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/../logs/error.log', 'Error in ensureAdminAccount: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    die('Error in ensureAdminAccount: ' . $e->getMessage());
}

// Cargar otras dependencias necesarias para las pruebas
// ...existing code...
