<?php
require_once '../controllers/auth.php';
require_once '../models/db.php';
require_once '../controllers/translations.php'; // Asegúrate de que este archivo existe
session_start();
requireLogin();

$userId = $_SESSION['user']['id'];

// Obtener lista de amigos
$stmt = $conn->prepare("SELECT u.id, u.username, f.status FROM friends f JOIN users u ON f.friend_id = u.id WHERE f.user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$friends = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Buscar usuario por nombre de usuario
$searchResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_username'])) {
    $searchUsername = sanitizeInput($_POST['search_username']);
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE username = ?");
    $stmt->bind_param("s", $searchUsername);
    $stmt->execute();
    $searchResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<?php include 'header.php'; ?>
<main style="margin-top: 0;"> <!-- Ajustar margen superior -->
    <div class="section">
        <h2><?= t('friends') ?></h2>
        <h3><?= t('your_friends') ?></h3>
        <ul style="list-style: none; padding: 0;">
            <?php if (count($friends) > 0): ?>
                <?php foreach ($friends as $friend): ?>
                    <li style="margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        <?= htmlspecialchars($friend['username']) ?> - <?= htmlspecialchars($friend['status']) ?>
                        <form method="POST" action="../routes/remove_friend.php" style="display: inline;">
                            <input type="hidden" name="friend_id" value="<?= $friend['id'] ?>">
                            <button type="submit" class="btn-danger" style="margin-left: 10px;"><?= t('remove') ?></button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p><?= t('no_friends') ?></p>
            <?php endif; ?>
        </ul>

        <h3><?= t('search_user') ?></h3>
        <form method="POST" style="margin-bottom: 20px;">
            <label for="search_username"><?= t('search_user') ?>:</label>
            <input type="text" id="search_username" name="search_username" placeholder="<?= t('username') ?>" required>
            <button type="submit" class="btn-primary"><?= t('search') ?></button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
