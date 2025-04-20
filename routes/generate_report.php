<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;
    $userId = $_SESSION['user']['id'];

    if ($startDate && $endDate) {
        $stmt = $conn->prepare("SELECT description, amount, currency, date FROM transactions WHERE user_id = ? AND date BETWEEN ? AND ?");
        $stmt->bind_param("iss", $userId, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $transactions = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode($transactions);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid date range']);
    }
}
?>
