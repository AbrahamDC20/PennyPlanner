<?php
require_once dirname(__DIR__) . '/models/db.php';

function getSpendingStatistics($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT SUM(amount) as total_spent, COUNT(*) as transaction_count FROM transactions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

function predictNextMonthSpending($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT AVG(amount) as avg_spending FROM transactions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['avg_spending'] * 1.1; // Predicción basada en promedio
}

function generateSpendingRecommendations($userId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT description, SUM(amount) as total_spent
        FROM transactions
        WHERE user_id = ?
        GROUP BY description
        ORDER BY total_spent DESC
        LIMIT 3
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $recommendations = [];

    while ($row = $result->fetch_assoc()) {
        $recommendations[] = "Consider reviewing your spending on: " . htmlspecialchars($row['description']) . ".";
    }

    $stmt->close();
    return $recommendations;
}

function getAIResponse($question) {
    // Simulación de respuesta de IA (puedes integrar un modelo real aquí)
    $responses = [
        'How do I reset my password?' => 'To reset your password, go to the reset password page and follow the instructions.',
        'How do I update my profile?' => 'To update your profile, navigate to the profile page and edit your details.',
    ];
    return $responses[$question] ?? 'I am sorry, I do not have an answer for that question.';
}

function getAIResponseFromAPI($question) {
    $apiKey = 'YOUR_API_KEY_HERE'; // Reemplazar con una clave válida
    $apiUrl = 'https://api.openai.com/v1/completions';

    $data = [
        'model' => 'text-davinci-003',
        'prompt' => $question,
        'max_tokens' => 150,
        'temperature' => 0.7,
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($apiUrl, false, $context);

    if ($response === false) {
        $error = error_get_last();
        throw new Exception('Error connecting to AI API: ' . $error['message']);
    }

    $responseData = json_decode($response, true);
    return $responseData['choices'][0]['text'] ?? 'No response from AI.';
}

function generatePersonalizedTutorial($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as transaction_count FROM transactions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $transactionCount = $result['transaction_count'] ?? 0;

    $tutorial = "Welcome to your personalized tutorial:\n";
    $tutorial .= "1. Learn how to navigate the homepage.\n";
    $tutorial .= "2. Update your profile information.\n";
    if ($transactionCount > 0) {
        $tutorial .= "3. Manage your transactions. You have already added $transactionCount transactions.\n";
    } else {
        $tutorial .= "3. Start by adding your first transaction.\n";
    }
    $tutorial .= "4. Customize your app settings to suit your preferences.\n";

    return $tutorial;
}
?>
