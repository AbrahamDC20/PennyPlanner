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

function generateSpendingRecommendations($userId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT description, SUM(amount) as total_spent
        FROM transactions
        WHERE user_id = ?
        GROUP BY description
        ORDER BY total_spent DESC
        LIMIT 3
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $recommendations = [];

    while ($row = $result->fetch_assoc()) {
        $recommendations[] = "Consider reviewing your spending on: " . htmlspecialchars($row['description']) . ".";
    }

    $stmt->close();
    return $recommendations;
}
?>
