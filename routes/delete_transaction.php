<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php'; // Incluir auth.php para definir requireLogin()
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionId = intval($_POST['transaction_id']);
    $userId = $_SESSION['user']['id'];

    // Verificar que la transacción pertenece al usuario actual
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $transactionId, $userId);
    $stmt->execute();
    $stmt->close();

    header('Location: ../views/transactions.php');
    exit();
}
