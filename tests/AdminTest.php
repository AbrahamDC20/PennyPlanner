<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/auth.php';

class AdminTest extends TestCase {
    protected function setUp(): void {
        global $conn;
        $conn->query("INSERT INTO users (id, username, password_hash, role) VALUES (1, 'testuser', 'testhash', 'user') ON DUPLICATE KEY UPDATE username='testuser'");
    }

    public function testEnsureAdminAccount() {
        $this->expectNotToPerformAssertions();
        ensureAdminAccount();
    }

    public function testDeleteUser() {
        deleteUser(1);
        global $conn;
        $result = $conn->query("SELECT * FROM users WHERE id = 1");
        $this->assertEquals(0, $result->num_rows);
    }
}
