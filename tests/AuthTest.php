<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/auth.php';

class AuthTest extends TestCase {
    protected function setUp(): void {
        global $conn;
        $conn->query("DELETE FROM users WHERE username = 'testuser'");
    }

    public function testRegisterUser() {
        registerUser('testuser', 'password123', 'Test', 'User', 'test@example.com', '123456789');
        global $conn;
        $result = $conn->query("SELECT * FROM users WHERE username = 'testuser'");
        $this->assertEquals(1, $result->num_rows);
    }

    public function testGetUser() {
        $user = getUser('testuser', 'password123');
        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user['username']);
    }

    public function testRequireLogin() {
        $_SESSION = [];
        $this->expectOutputRegex('/Location: .*login\.php/');
        requireLogin();
    }
}
