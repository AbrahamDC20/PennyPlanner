<?php
session_start();
require_once '../controllers/aiController.php';
require_once '../controllers/translations.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = htmlspecialchars($_POST['question']);
    if (!isset($_SESSION['chat_history'])) {
        $_SESSION['chat_history'] = [];
    }

    if (!empty($question)) {
        $_SESSION['chat_history'][] = ['sender' => 'user', 'text' => $question];
        try {
            $response = getAIResponseFromAPI($question);
            $_SESSION['chat_history'][] = ['sender' => 'ai', 'text' => $response];
        } catch (Exception $e) {
            $_SESSION['chat_history'][] = ['sender' => 'ai', 'text' => t('error_generating_response')];
        }
    } else {
        $_SESSION['chat_history'][] = ['sender' => 'ai', 'text' => t('invalid_input')];
    }

    header('Location: ../views/customer_support.php');
    exit();
}
?>
