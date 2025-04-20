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
<main>
    <div class="section">
        <h2><?= t('friends') ?></h2>
        <ul>
            <?php foreach ($friends as $friend): ?>
                <li>
                    <?= htmlspecialchars($friend['username']) ?> - <?= htmlspecialchars($friend['status']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
<?php include 'footer.php'; ?>
