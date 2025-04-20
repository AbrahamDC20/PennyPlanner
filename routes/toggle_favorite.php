<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';
requireLogin();

$userId = $_SESSION['user']['id'];
$transactionId = intval($_POST['transaction_id']);

$stmt = $conn->prepare("SELECT id FROM favorite_transactions WHERE user_id = ? AND transaction_id = ?");
$stmt->bind_param("ii", $userId, $transactionId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_assoc()) {
    $stmt = $conn->prepare("DELETE FROM favorite_transactions WHERE user_id = ? AND transaction_id = ?");
    $stmt->bind_param("ii", $userId, $transactionId);
} else {
    $stmt = $conn->prepare("INSERT INTO favorite_transactions (user_id, transaction_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $transactionId);
}
$stmt->execute();
$stmt->close();

header('Location: ../views/transactions.php');
exit();
