<?php
require_once '../models/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $transactions = getTransactions();
    echo json_encode($transactions);
}
?>
