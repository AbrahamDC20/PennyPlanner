<?php
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/models/db.php'; // Incluir el archivo necesario

class TransactionTest extends TestCase {
    public function testAddTransaction() {
        global $conn;
        $userId = 1;
        $description = 'Test Transaction';
        $amount = 100.50;
        $currency = 'USD';

        $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, currency) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $userId, $description, $amount, $currency);
        $this->assertTrue($stmt->execute());
        $stmt->close();
    }

    public function testGetTransactions() {
        global $conn;
        $userId = 1;

        // Insertar datos de prueba
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, currency) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $userId, $description = 'Test Transaction', $amount = 100.50, $currency = 'USD');
        $stmt->execute();
        $stmt->close();

        $transactions = getTransactions($userId);
        $this->assertIsArray($transactions);
        $this->assertNotEmpty($transactions);
    }
}
