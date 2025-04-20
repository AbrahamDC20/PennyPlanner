<?php
require_once dirname(__DIR__) . '/models/db.php';

function generateTutorial($username) {
    return "Hola $username, bienvenido a Budget Buddy. Aquí tienes un tutorial sobre cómo usar la aplicación:
    1. Navega por la página de inicio para ver tus transacciones recientes.
    2. Ve a la sección de perfil para actualizar tu información personal.
    3. Usa la sección de transacciones para agregar, editar o eliminar transacciones.
    4. Personaliza la configuración de la aplicación en la sección de configuración.";
}

function saveTutorial($userId, $content) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO tutorials (user_id, content) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $content);
    $stmt->execute();
    $stmt->close();
}

function getTutorial($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT content FROM tutorials WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['content'] ?? null;
}
?>
