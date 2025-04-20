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

$conn->query("
    CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS role_permissions (
        role_id INT NOT NULL,
        permission_id INT NOT NULL,
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
        FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
        PRIMARY KEY (role_id, permission_id)
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS user_roles (
        user_id INT NOT NULL,
        role_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
        PRIMARY KEY (user_id, role_id)
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS profile_changes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        field_changed VARCHAR(50) NOT NULL,
        old_value TEXT,
        new_value TEXT,
        change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS audit_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS favorite_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        transaction_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS friends (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        friend_id INT NOT NULL,
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS account_recovery_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        type ENUM('success', 'error', 'info') DEFAULT 'info',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS user_activity (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        activity VARCHAR(255) NOT NULL,
        activity_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
$conn->query("
    CREATE INDEX IF NOT EXISTS idx_transactions_currency ON transactions(currency)
");

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

function getExchangeRate($fromCurrency, $toCurrency) {
    // Simulación de tasas de cambio (puedes reemplazar esto con una API real)
    $exchangeRates = [
        'USD' => ['USD' => 1, 'EUR' => 0.85, 'GBP' => 0.75],
        'EUR' => ['USD' => 1.18, 'EUR' => 1, 'GBP' => 0.88],
        'GBP' => ['USD' => 1.33, 'EUR' => 1.14, 'GBP' => 1],
    ];
    // ...existing code...
}

?>