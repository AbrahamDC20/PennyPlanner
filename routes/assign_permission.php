<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roleId = intval($_POST['role_id']);
    $permissionId = intval($_POST['permission_id']);
    assignPermissionToRole($roleId, $permissionId);
    header('Location: ../views/roles_permissions.php');
    exit();
}
?>
