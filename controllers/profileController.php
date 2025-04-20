<?php
require_once dirname(__DIR__) . '/models/db.php'; // Use require_once to avoid duplicate inclusion

function updateProfileImage($userId, $imageFile) {
    // Verificar si el archivo fue subido correctamente
    if (isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
        if ($imageFile['size'] > 2 * 1024 * 1024) { // Limitar a 2 MB
            throw new Exception("File size exceeds 2 MB.");
        }
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($imageFile['type'], $allowedTypes)) {
            throw new Exception("Invalid file type.");
        }

        $uploadDir = dirname(__DIR__) . '/uploads/'; // Directorio donde se guardarán las imágenes
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Crear el directorio si no existe
        }

        $fileName = basename($imageFile['name']); // Obtener el nombre del archivo
        $targetPath = $uploadDir . $fileName; // Ruta completa del archivo

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
            // Actualizar la ruta de la imagen en la base de datos
            updateProfileImageInDB($userId, $fileName);
        } else {
            throw new Exception("Error al mover el archivo subido.");
        }
    } else {
        throw new Exception("No se subió ningún archivo válido.");
    }
}

function logProfileChange($userId, $field, $oldValue, $newValue) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO profile_changes (user_id, field_changed, old_value, new_value) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $field, $oldValue, $newValue);
    $stmt->execute();
    $stmt->close();
}

function getProfileChanges($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT field_changed, old_value, new_value, change_date FROM profile_changes WHERE user_id = ? ORDER BY change_date DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $changes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $changes;
}
?>