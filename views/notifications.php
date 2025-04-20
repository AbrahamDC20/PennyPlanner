<?php
require_once '../controllers/auth.php';
require_once '../controllers/notificationsController.php';
session_start();
requireLogin();

$userId = $_SESSION['user']['id'];
$notifications = getNotifications($userId);
?>

<?php include 'header.php'; ?>
<main>
    <div class="section">
        <h2><?= t('notifications') ?></h2>
        <ul>
            <?php foreach ($notifications as $notification): ?>
                <li class="<?= $notification['is_read'] ? 'read' : 'unread' ?>">
                    <?= htmlspecialchars($notification['message']) ?> - <?= htmlspecialchars($notification['created_at']) ?>
                    <?php if (!$notification['is_read']): ?>
                        <form method="POST" action="../routes/mark_notification.php" style="display: inline;">
                            <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                            <button type="submit"><?= t('mark_as_read') ?></button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
<?php include 'footer.php'; ?>
