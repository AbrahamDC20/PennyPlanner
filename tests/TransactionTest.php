<?php
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/models/db.php';

class TransactionTest extends TestCase {
    protected function setUp(): void {
        global $conn;
        // Eliminar transacciones y usuarios relacionados con el usuario de prueba
        $conn->query("DELETE FROM transactions WHERE user_id = 1");
        $conn->query("DELETE FROM users WHERE id = 1");

        // Insertar el usuario de prueba con datos completos
        $conn->query("
            INSERT INTO users (id, username, password_hash, first_name, last_name, email, phone, role) 
            VALUES (1, 'testuser', 'testhash', 'Test', 'User', 'testuser@example.com', '123456789', 'user')
        ");
    }

    public function testAddTransaction() {
        try {
            global $conn;
            $userId = 1; // Asegurarse de que este ID existe en la tabla `users`
            $description = 'Test Transaction';
            $amount = 100.50;
            $currency = 'USD';

            $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, currency) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isds", $userId, $description, $amount, $currency);
            $this->assertTrue($stmt->execute());
            $stmt->close();
        } catch (Exception $e) {
            $this->fail("Exception in testAddTransaction: " . $e->getMessage());
        }
    }

    public function testGetTransactions() {
        try {
            global $conn;
            $userId = 1;

            // Insertar una transacción de prueba
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, currency) VALUES (?, ?, ?, ?)");
            $description = 'Test Transaction';
            $amount = 100.50;
            $currency = 'USD';
            $stmt->bind_param("isds", $userId, $description, $amount, $currency);
            $stmt->execute();
            $stmt->close();

            // Obtener las transacciones
            $transactions = getTransactions($userId);
            $this->assertIsArray($transactions);
            $this->assertNotEmpty($transactions);
        } catch (Exception $e) {
            $this->fail("Exception in testGetTransactions: " . $e->getMessage());
        }
    }
}
