<?php
use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase {
    public function testEnsureAdminAccount() {
        $this->expectNotToPerformAssertions();
        ensureAdminAccount();
    }

    public function testDeleteUser() {
        $this->expectNotToPerformAssertions();
        deleteUser(1); // Asegúrate de que el ID 1 existe en la base de datos
    }
}
