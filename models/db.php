<?php
// Configuración de la conexión a la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'budget_buddy'; // Nombre de la base de datos

// Manejo de errores con registro en archivo
function logError($error) {
    $logFile = dirname(__DIR__) . '/logs/error.log';
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0777, true);
    }
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - $error\n", FILE_APPEND);
}

// Verifica que la conexión a la base de datos esté funcionando correctamente
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    logError('Database connection failed: ' . $conn->connect_error);
    die('Database connection failed. Please try again later.');
}

// Función para sanitizar entradas
function sanitizeInput($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

// Función para obtener transacciones
function getTransactions($userId, $limit = 10, $offset = 0) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? LIMIT ? OFFSET ?");
    $stmt->bind_param("iii", $userId, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $transactions;
}

// Función para obtener amigos
function getFriends($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT u.id, u.username, u.first_name, u.last_name, u.profile_image, f.status
                            FROM friends f
                            JOIN users u ON f.friend_id = u.id
                            WHERE f.user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para listar usuarios
function listUsers() {
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, email FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $users;
}

// Asegurar que la cuenta de administrador exista
function ensureAdminAccount() {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'Abraham' AND role = 'admin'");
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$admin) {
        $passwordHash = password_hash('17Del12Del2004', PASSWORD_DEFAULT);
        $stmtInsert = $conn->prepare("INSERT INTO users (username, password_hash, first_name, last_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("sssssss", $username, $passwordHash, $firstName, $lastName, $email, $phone, $role);
        $username = 'Abraham';
        $firstName = 'Abraham';
        $lastName = 'Díaz';
        $email = 'pennyplanner2025@gmail.com';
        $phone = '722683559';
        $role = 'admin';
        $stmtInsert->execute();
        $stmtInsert->close();
    }
}

// Asegurar que el esquema de la base de datos esté actualizado
function ensureDatabaseSchema() {
    global $conn;

    // Verificar si la columna 2fa_enabled existe en la tabla users
    $result = $conn->query("SHOW COLUMNS FROM users LIKE '2fa_enabled'");
    if ($result->num_rows === 0) {
        $conn->query("ALTER TABLE users ADD COLUMN 2fa_enabled TINYINT(1) DEFAULT 0");
    }

    // Asegurar integridad referencial en la tabla transactions
    $conn->query("ALTER TABLE transactions ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
}

// Función para obtener roles
function getRoles() {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name FROM roles");
    $stmt->execute();
    $result = $stmt->get_result();
    $roles = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $roles;
}

// Función para obtener permisos
function getPermissions() {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name FROM permissions");
    $stmt->execute();
    $result = $stmt->get_result();
    $permissions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $permissions;
}

// Función para asignar permisos a un rol
function assignPermissionToRole($roleId, $permissionId) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE role_id=role_id");
    $stmt->bind_param("ii", $roleId, $permissionId);
    $stmt->execute();
    $stmt->close();
}

// Función para eliminar permisos de un rol
function removePermissionFromRole($roleId, $permissionId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?");
    $stmt->bind_param("ii", $roleId, $permissionId);
    $stmt->execute();
    $stmt->close();
}

// Llamar a las funciones para inicializar
ensureAdminAccount();
ensureDatabaseSchema();
?>