<?php
// Configuración de la conexión a la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'budget_buddy'; // Nombre de la base de datos

// Manejo de errores con registro en archivo
if (!function_exists('logError')) { // Agregado para evitar redeclaración
    function logError($error) {
        $logFile = dirname(__DIR__) . '/logs/error.log';
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - $error\n", FILE_APPEND);
    }
}

// Mejorar ensureAdminAccount con validación y manejo de errores
if (!function_exists('ensureAdminAccount')) {
    function ensureAdminAccount() {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = 'Abraham' AND role = 'admin'");
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            $stmt->close();

            if ($admin) {
                // Verificar si el hash es válido
                if (!password_verify('17Del12Del2004', $admin['password_hash'])) {
                    $newHash = password_hash('17Del12Del2004', PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $newHash, $admin['id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            } else {
                $passwordHash = password_hash('17Del12Del2004', PASSWORD_DEFAULT);
                $stmtInsert = $conn->prepare("INSERT INTO users (username, password_hash, first_name, last_name, email, phone, profile_image, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $username = 'Abraham';
                $firstName = 'Abraham';
                $lastName = 'Díaz';
                $email = 'pennyplanner2025@gmail.com';
                $phone = '722683559';
                $profileImage = NULL;
                $role = 'admin';
                $stmtInsert->bind_param("ssssssss", $username, $passwordHash, $firstName, $lastName, $email, $phone, $profileImage, $role);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
        } catch (Exception $e) {
            logError("Error in ensureAdminAccount: " . $e->getMessage());
        }
    }
}

// Verifica que la conexión a la base de datos esté funcionando correctamente
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    logError('Database connection failed: ' . $conn->connect_error);
    die('Database connection failed. Please try again later.');
}

// Crear la tabla `tutorials` si no existe
$conn->query("
    CREATE TABLE IF NOT EXISTS tutorials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

// Crear índices si no existen
$conn->query("
    CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)
");
$conn->query("
    CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)
");
$conn->query("
    CREATE INDEX IF NOT EXISTS idx_transactions_date ON transactions(date)
");
$conn->query("
    CREATE INDEX IF NOT EXISTS idx_transactions_user_id ON transactions(user_id)
");

// Asegurarse de que la cuenta de administrador exista (llamar explícitamente en lugar de automáticamente)
// ensureAdminAccount();

// Centralizar configuración de la base de datos
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        global $conn;
        return $conn;
    }
}

// Validar entrada para evitar inyecciones SQL
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        global $conn;
        return $conn->real_escape_string(trim($input));
    }
}

// Actualizar imagen de perfil del usuario (solo la operación en la base de datos)
if (!function_exists('updateProfileImageInDB')) {
    function updateProfileImageInDB($userId, $fileName) {
        global $conn;
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $userId);
        $stmt->execute();
        $stmt->close();
    }
}

// Optimizar consulta de transacciones con índice en la columna 'date'
function getTransactions($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $transactions;
}

// Obtener configuraciones de la IA para un usuario
function getAISettings($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM ai_settings WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener lista de amigos de un usuario
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

// Listar usuarios
if (!function_exists('listUsers')) {
    function listUsers() {
        global $conn;
        $stmt = $conn->prepare("SELECT id, username, email FROM users"); // Incluir el campo email
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $users;
    }
}

// Asignar rol a un usuario
function assignRole($userId, $role) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $userId);
    $stmt->execute();
    $stmt->close();
}
?>