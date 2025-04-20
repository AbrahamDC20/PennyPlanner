<?php
require_once __DIR__ . '/../models/db.php'; // Cambiar a ruta absoluta
require_once __DIR__ . '/tutorialController.php'; // Cambiar a ruta absoluta

// Registrar un nuevo usuario
function registerUser($username, $password, $firstName, $lastName, $email, $phone) {
    global $conn;
    try {
        if (empty($username) || empty($password) || empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
            throw new Exception(t('both_fields_required'));
        }
        $username = sanitizeInput($username);
        $email = sanitizeInput($email);
        $phone = sanitizeInput($phone);

        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->fetch()) {
            $stmt->close();
            throw new Exception(t('username_exists'));
        }
        $stmt->close();

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->fetch()) {
            $stmt->close();
            throw new Exception(t('email_exists'));
        }
        $stmt->close();

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, first_name, last_name, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $passwordHash, $firstName, $lastName, $email, $phone);
        if (!$stmt->execute()) {
            throw new Exception('Error executing query: ' . $stmt->error);
        }
        $stmt->close();

        // Generar tutorial personalizado
        $tutorialContent = generateTutorial($username);
        saveTutorial($conn->insert_id, $tutorialContent); // Guardar tutorial en la base de datos

        // Enviar notificación de bienvenida
        notifyUserByEmail($email, t('welcome'), t('welcome_message'));
    } catch (Exception $e) {
        logError('Error registering user: ' . $e->getMessage());
        throw $e;
    }
}

// Obtener usuario por nombre de usuario y verificar contraseña
function getUser($username, $password) {
    global $conn;
    try {
        if (empty($username) || empty($password)) {
            throw new Exception('Username and password are required.');
        }
        $username = sanitizeInput($username);
        $password = trim($password); // Eliminar espacios en blanco adicionales
        logError("Sanitized username: $username"); // Log para depuración
        logError("Provided password (trimmed): $password"); // Log para depuración

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            logError("User found: " . json_encode($user)); // Log para depuración
            logError("Stored hash: " . $user['password_hash']); // Log para depuración
            $passwordVerifyResult = password_verify($password, $user['password_hash']);
            logError("Password verification result for user $username: " . ($passwordVerifyResult ? "success" : "failure"));

            if ($passwordVerifyResult) {
                logError("Password verification successful for user: $username");
                // Actualizar el hash si es necesario
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $newHash, $user['id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                    logError("Password hash updated for user: $username");
                }
                unset($user['password_hash']);
                return $user;
            } else {
                logError("Password verification failed for user: $username. Provided password: $password, Hash: " . $user['password_hash']);
            }
        } else {
            logError("User not found: $username");
        }

        return null;
    } catch (Exception $e) {
        logError("Error in getUser: " . $e->getMessage());
        throw new Exception('Error fetching user.');
    }
}

// Verificar si el usuario ha iniciado sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: /Website_Technologies_Abraham/Final_Proyect/views/login.php');
        exit();
    }
}

// Verificar roles
function requireRole($role) {
    requireLogin();
    if ($_SESSION['user']['role'] !== $role) {
        header('Location: /Website_Technologies_Abraham/Final_Proyect/views/index.php');
        exit();
    }
}

// Cerrar sesión
function logout() {
    session_start();
    session_unset();
    session_destroy();
}

// Función de administrador: eliminar usuario por ID
function deleteUser($userId) {
    global $conn;
    try {
        if (empty($userId)) {
            throw new Exception('User ID is required.');
        }
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        logError('Error deleting user: ' . $e->getMessage());
        throw new Exception('Error deleting user.');
    }
}

// Enviar correo de restablecimiento de contraseña
function sendPasswordResetEmail($email) {
    global $conn;
    $email = sanitizeInput($email);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception(t('email_not_found'));
    }

    $token = bin2hex(random_bytes(16));
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token) VALUES (?, ?)");
    $stmt->bind_param("is", $user['id'], $token);
    $stmt->execute();
    $stmt->close();

    $resetLink = "http://yourwebsite.com/Website_Technologies_Abraham/Final_Proyect/views/change_password.php?token=$token";

    // Configuración del correo
    $appEmail = "pennyplanner2025@gmail.com";
    $appPassword = "Administrador";
    $subject = t('reset_password');
    $message = t('click_link') . ": $resetLink";
    $headers = "From: PennyPlanner <$appEmail>\r\n";
    $headers .= "Reply-To: $appEmail\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Usar la función mail para enviar el correo
    if (!mail($email, $subject, $message, $headers)) {
        throw new Exception(t('email_send_error'));
    }
}

// Restablecer contraseña
function resetPassword($token, $newPassword) {
    global $conn;

    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $reset = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$reset) {
        throw new Exception(t('invalid_token'));
    }

    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $passwordHash, $reset['user_id']);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();
}

// Notificar al usuario por correo electrónico
function notifyUserByEmail($email, $subject, $message) {
    $headers = "From: PennyPlanner <pennyplanner2025@gmail.com>\r\n";
    $headers .= "Reply-To: pennyplanner2025@gmail.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (!mail($email, $subject, $message, $headers)) {
        logError("Failed to send email to $email");
    }
}

// Verificar código 2FA
function verify2FACode($userId, $inputCode) {
    global $conn;
    $stmt = $conn->prepare("SELECT 2fa_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result && $result['2fa_code'] === $inputCode;
}

function sendEmailNotification($email, $subject, $message) {
    ob_start();
    include dirname(__DIR__) . '/templates/email_template.php';
    $htmlMessage = ob_get_clean();

    $headers = "From: PennyPlanner <no-reply@pennyplanner.com>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    mail($email, $subject, $htmlMessage, $headers);
}

function logUserActivity($userId, $activity) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO user_activity (user_id, activity) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $activity);
    $stmt->execute();
    $stmt->close();
}

function createAccountRecoveryRequest($email) {
    global $conn;

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception(t('email_not_found'));
    }

    $token = bin2hex(random_bytes(32));
    $stmt = $conn->prepare("INSERT INTO account_recovery_requests (user_id, token) VALUES (?, ?)");
    $stmt->bind_param("is", $user['id'], $token);
    $stmt->execute();
    $stmt->close();

    $recoveryLink = "http://yourwebsite.com/Website_Technologies_Abraham/Final_Proyect/views/recover_account.php?token=$token";
    notifyUserByEmail($email, t('recover_account'), t('click_link') . ": $recoveryLink");
}

function verifyRecoveryToken($token) {
    global $conn;

    $stmt = $conn->prepare("SELECT user_id FROM account_recovery_requests WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$request) {
        throw new Exception(t('invalid_token'));
    }

    return $request['user_id'];
}

function assignRoleToUser($userId, $roleId) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $roleId);
    $stmt->execute();
    $stmt->close();
}

function checkPermission($userId, $permissionName) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 1
        FROM user_roles ur
        JOIN role_permissions rp ON ur.role_id = rp.role_id
        JOIN permissions p ON rp.permission_id = p.id
        WHERE ur.user_id = ? AND p.name = ?
    ");
    $stmt->bind_param("is", $userId, $permissionName);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result ? true : false;
}

function logAudit($userId, $action) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $action);
    $stmt->execute();
    $stmt->close();
}
?>