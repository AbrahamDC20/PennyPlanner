<?php
require_once '../controllers/auth.php';
require_once '../models/db.php';
session_start();
requireLogin();

$userId = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT u.id, u.username, f.status FROM friends f JOIN users u ON f.friend_id = u.id WHERE f.user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$friends = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<?php include 'header.php'; ?>
<main style="margin: 80px auto; max-width: 800px; padding: 20px;">
    <div class="section">
        <h2><?= t('friends') ?></h2>
        <form method="POST" action="../routes/add_friend.php" style="margin-bottom: 20px;">
            <label for="friend_username"><?= t('add_friend') ?>:</label>
            <input type="text" id="friend_username" name="friend_username" placeholder="<?= t('username') ?>" required>
            <button type="submit" class="btn-primary"><?= t('add') ?></button>
        </form>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($friends as $friend): ?>
                <li style="margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <?= htmlspecialchars($friend['username']) ?> - <?= htmlspecialchars($friend['status']) ?>
                    <form method="POST" action="../routes/remove_friend.php" style="display: inline;">
                        <input type="hidden" name="friend_id" value="<?= $friend['id'] ?>">
                        <button type="submit" class="btn-danger" style="margin-left: 10px;"><?= t('remove') ?></button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
<?php include 'footer.php'; ?>
