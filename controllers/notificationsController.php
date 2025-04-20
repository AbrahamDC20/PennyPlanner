<?php
require_once dirname(__DIR__) . '/models/db.php';

function createNotification($userId, $message, $type = 'info', $relatedId = null) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, type, related_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $userId, $message, $type, $relatedId);
    $stmt->execute();
    $stmt->close();
}

function getNotifications($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, message, type, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $notifications;
}

function markNotificationAsRead($notificationId) {
    global $conn;
    $stmt = $conn->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $stmt->close();
}

function createFriendRequestNotification($userId, $friendRequestId) {
    createNotification($userId, t('friend_request_received'), 'friend_request', $friendRequestId);
}

function handleFriendRequestResponse($notificationId, $response) {
    global $conn;
    $stmt = $conn->prepare("SELECT related_id FROM notifications WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $notification = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($notification) {
        $friendRequestId = $notification['related_id'];
        $status = $response === 'accept' ? 'accepted' : 'rejected';
        $stmt = $conn->prepare("UPDATE friends SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $friendRequestId);
        $stmt->execute();
        $stmt->close();
    }
}
?>
