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

// Verifica que la conexión a la base de datos esté funcionando correctamente
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    logError('Database connection failed: ' . $conn->connect_error);
    die('Database connection failed. Please try again later.');
}

// Función para sanitizar entradas
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        global $conn;
        return $conn->real_escape_string(trim($input));
    }
}

// Función para actualizar la imagen de perfil en la base de datos
if (!function_exists('updateProfileImageInDB')) {
    function updateProfileImageInDB($userId, $fileName) {
        global $conn;
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $userId);
        $stmt->execute();
        $stmt->close();
    }
}

// Función para obtener transacciones
if (!function_exists('getTransactions')) {
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
}

// Función para obtener amigos
if (!function_exists('getFriends')) {
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
}

// Función para listar usuarios
if (!function_exists('listUsers')) {
    function listUsers() {
        global $conn;
        $stmt = $conn->prepare("SELECT id, username, email FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $users;
    }
}

if (!function_exists('ensureAdminAccount')) {
    function ensureAdminAccount() {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'Abraham' AND role = 'admin'");
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$admin) {
                $passwordHash = password_hash('17Del12Del2004', PASSWORD_DEFAULT);
                $stmtInsert = $conn->prepare("INSERT INTO users (username, password_hash, first_name, last_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $username = 'Abraham';
                $firstName = 'Abraham';
                $lastName = 'Díaz';
                $email = 'pennyplanner2025@gmail.com';
                $phone = '722683559';
                $role = 'admin';
                $stmtInsert->bind_param("sssssss", $username, $passwordHash, $firstName, $lastName, $email, $phone, $role);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
        } catch (Exception $e) {
            logError("Error in ensureAdminAccount: " . $e->getMessage());
        }
    }
}

// Llamar a la función para asegurarse de que la cuenta de administrador exista
ensureAdminAccount();
?>