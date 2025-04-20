<?php
use PHPUnit\Framework\TestCase;

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
        $this->expectException(Exception::class);
        requireLogin();
    }
}
