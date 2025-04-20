<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php'; // Incluir auth.php para definir requireLogin()
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = sanitizeInput($_POST['description']);
    $amount = floatval($_POST['amount']);
    $currency = sanitizeInput($_POST['currency']);
    $userId = $_SESSION['user']['id'];

    // Eliminar la conversión de moneda
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, currency) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $userId, $description, $amount, $currency);
    $stmt->execute();
    $stmt->close();

    header('Location: ../views/transactions.php');
    exit();
}
