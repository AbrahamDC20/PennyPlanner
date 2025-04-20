<?php
require_once dirname(__DIR__) . '/models/db.php';

// Inicializar la conexión a la base de datos para pruebas
global $conn;
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Cargar otras dependencias necesarias para las pruebas
