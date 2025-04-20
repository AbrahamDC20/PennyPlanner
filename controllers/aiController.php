<?php
require_once dirname(__DIR__) . '/models/db.php';

function getSpendingStatistics($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT SUM(amount) as total_spent, COUNT(*) as transaction_count FROM transactions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

function predictNextMonthSpending($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT AVG(amount) as avg_spending FROM transactions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['avg_spending'] * 1.1; // Predicción basada en promedio
}
?>
