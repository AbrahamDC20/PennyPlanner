<?php
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase {
    public function testAddTransaction() {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, currency) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $userId = 1, $description = 'Test Transaction', $amount = 100.50, $currency = 'USD');
        $this->assertTrue($stmt->execute());
        $stmt->close();
    }

    public function testGetTransactions() {
        $transactions = getTransactions(1);
        $this->assertIsArray($transactions);
        $this->assertNotEmpty($transactions);
    }
}
