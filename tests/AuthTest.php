<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/auth.php'; // Cambiar a ruta absoluta

class AuthTest extends TestCase {
    public function testRegisterUser() {
        $this->expectNotToPerformAssertions();
        registerUser('testuser', 'password123', 'Test', 'User', 'test@example.com', '123456789');
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
