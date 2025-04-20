<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/auth.php';

class AuthTest extends TestCase {
    protected function setUp(): void {
        global $conn;
        $conn->query("DELETE FROM users WHERE username = 'testuser'");
        $passwordHash = password_hash('password123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, password_hash, first_name, last_name, email, phone, role) VALUES ('testuser', '$passwordHash', 'Test', 'User', 'test@example.com', '123456789', 'user')");
    }

    public function testRegisterUser() {
        try {
            registerUser('newuser', 'password123', 'New', 'User', 'new@example.com', '987654321');
            global $conn;
            $result = $conn->query("SELECT * FROM users WHERE username = 'newuser'");
            $this->assertEquals(1, $result->num_rows);
        } catch (Exception $e) {
            $this->fail("Exception in testRegisterUser: " . $e->getMessage());
        }
    }

    public function testGetUser() {
        try {
            $user = getUser('testuser', 'password123');
            $this->assertNotNull($user);
            $this->assertEquals('testuser', $user['username']);
        } catch (Exception $e) {
            $this->fail("Exception in testGetUser: " . $e->getMessage());
        }
    }

    public function testRequireLogin() {
        try {
            $_SESSION = [];
            $this->expectOutputRegex('/Location: .*login\.php/');
            requireLogin();
        } catch (Exception $e) {
            $this->fail("Exception in testRequireLogin: " . $e->getMessage());
        }
    }
}
