<?php
require_once __DIR__ . '/../models/db.php'; // Cambiar a ruta absoluta

// Inicializar la conexión a la base de datos para pruebas
global $conn;
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Asegurarse de que la cuenta de administrador exista para las pruebas
ensureAdminAccount();

// Cargar otras dependencias necesarias para las pruebas
