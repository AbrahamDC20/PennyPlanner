<?php
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/controllers/auth.php';

class RolePermissionTest extends TestCase {
    public function testAssignRoleToUser() {
        global $conn;
        $conn->query("INSERT INTO roles (id, name) VALUES (1, 'admin') ON DUPLICATE KEY UPDATE name='admin'");
        assignRoleToUser(1, 1);
        $stmt = $conn->prepare("SELECT * FROM user_roles WHERE user_id = 1 AND role_id = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $this->assertEquals(1, $result->num_rows);
        $stmt->close();
    }

    public function testCheckPermission() {
        global $conn;
        $conn->query("INSERT INTO permissions (id, name) VALUES (1, 'view_reports') ON DUPLICATE KEY UPDATE name='view_reports'");
        $conn->query("INSERT INTO role_permissions (role_id, permission_id) VALUES (1, 1) ON DUPLICATE KEY UPDATE role_id=1");
        $this->assertTrue(checkPermission(1, 'view_reports'));
    }

    public function testRemovePermissionFromRole() {
        global $conn;
        $conn->query("INSERT INTO roles (id, name) VALUES (1, 'admin') ON DUPLICATE KEY UPDATE name='admin'");
        $conn->query("INSERT INTO permissions (id, name) VALUES (1, 'view_reports') ON DUPLICATE KEY UPDATE name='view_reports'");
        assignPermissionToRole(1, 1);
        removePermissionFromRole(1, 1);
        $stmt = $conn->prepare("SELECT * FROM role_permissions WHERE role_id = 1 AND permission_id = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $this->assertEquals(0, $result->num_rows);
        $stmt->close();
    }
}
?>
