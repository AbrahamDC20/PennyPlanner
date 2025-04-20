<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/auth.php';

class AdminTest extends TestCase {
    protected function setUp(): void {
        global $conn;
        $conn->query("INSERT INTO users (id, username, password_hash, role) VALUES (1, 'testuser', 'testhash', 'user') ON DUPLICATE KEY UPDATE username='testuser'");
    }

    public function testEnsureAdminAccount() {
        try {
            ensureAdminAccount();
            global $conn;
            $result = $conn->query("SELECT * FROM users WHERE username = 'Abraham' AND role = 'admin'");
            $this->assertEquals(1, $result->num_rows);
        } catch (Exception $e) {
            $this->fail("Exception in testEnsureAdminAccount: " . $e->getMessage());
        }
    }

    public function testDeleteUser() {
        try {
            deleteUser(1);
            global $conn;
            $result = $conn->query("SELECT * FROM users WHERE id = 1");
            $this->assertEquals(0, $result->num_rows);
        } catch (Exception $e) {
            $this->fail("Exception in testDeleteUser: " . $e->getMessage());
        }
    }
}
