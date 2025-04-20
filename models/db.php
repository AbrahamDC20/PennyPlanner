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
            $stmt->bind_result($adminId, $storedHash);
            if ($stmt->fetch()) {
                $stmt->close(); // Cerrar el statement antes de continuar
                logError("Admin account already exists. Username: Abraham, Stored Hash: $storedHash");
                // Verificar si el hash es válido
                if (!password_verify('17Del12Del2004', $storedHash)) {
                    logError("Invalid hash detected for admin. Regenerating hash...");
                    $newHash = password_hash('17Del12Del2004', PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $newHash, $adminId);
                    $updateStmt->execute();
                    $updateStmt->close();
                    logError("Admin password hash successfully regenerated.");
                }
            } else {
                $stmt->close(); // Cerrar el statement antes de insertar
                $passwordHash = password_hash('17Del12Del2004', PASSWORD_DEFAULT);
                logError("Generated password hash for admin: $passwordHash");
                $stmtInsert = $conn->prepare("INSERT INTO users (username, password_hash, first_name, last_name, email, phone, profile_image, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtInsert->bind_param(
                    "ssssssss",
                    $username = 'Abraham',
                    $passwordHash,
                    $firstName = 'Abraham',
                    $lastName = 'Díaz',
                    $email = 'pennyplanner2025@gmail.com',
                    $phone = '722683559',
                    $profileImage = NULL,
                    $role = 'admin'
                );
                if ($stmtInsert->execute()) {
                    logError("Admin account created successfully with username: $username");
                } else {
                    logError("Failed to create admin account: " . $stmtInsert->error);
                }
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

// Asegurarse de que la cuenta de administrador exista
ensureAdminAccount();

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
function getTransactions($limit = 10, $offset = 0) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT description, amount, currency FROM transactions ORDER BY date DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        logError($e->getMessage());
        return [];
    }
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
        $stmt = $conn->prepare("SELECT id, username FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $users;
    }
}
?>