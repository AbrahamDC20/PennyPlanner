<?php
require_once dirname(__DIR__) . '/models/db.php';

function generateTutorial() {
    return [
        ['message' => t('tutorial_step_1'), 'element' => '#dashboard'],
        ['message' => t('tutorial_step_2'), 'element' => '#profile'],
        ['message' => t('tutorial_step_3'), 'element' => '#transactions'],
        ['message' => t('tutorial_step_4'), 'element' => '#settings'],
    ];
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
