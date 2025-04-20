<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/profileController.php';
require_once dirname(__DIR__) . '/controllers/aiController.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
requireLogin();
$recommendations = generateSpendingRecommendations($_SESSION['user']['id']);
?>

<?php include 'header.php'; ?>
<main style="margin-top: 0; background-color: #f9f9f9;"> <!-- Ajustar fondo -->
    <div class="section">
        <h2><?= t('profile') ?></h2>
        <div class="profile-header" style="display: flex; align-items: center; gap: 20px;"> <!-- Align image and name -->
            <div class="profile-image-container">
                <img src="<?= isset($_SESSION['user']['profile_image']) ? '/Website_Technologies_Abraham/Final_Proyect/uploads/' . htmlspecialchars($_SESSION['user']['profile_image']) : '/Website_Technologies_Abraham/Final_Proyect/assets/default-profile.png' ?>" 
                     alt="<?= t('profile_image') ?>" 
                     class="profile-image"> <!-- Asegurarse de usar la clase correcta -->
            </div>
            <div class="profile-username">
                <?= htmlspecialchars($_SESSION['user']['username']) ?>
            </div>
        </div>
        <form method="POST" action="../routes/update_profile.php" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
            <label for="username"><?= t('username') ?>:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($_SESSION['user']['username']) ?>" required>
            <label for="email"><?= t('email') ?>:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" required>
            <label for="phone"><?= t('phone') ?>:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($_SESSION['user']['phone']) ?>">
            <label for="profile_image"><?= t('profile_image') ?>:</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*">
            <button type="submit"><?= t('update') ?></button> <!-- Ensure button is visible -->
        </form>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <h3><?= t('profile_changes') ?></h3>
        <table>
            <thead>
                <tr>
                    <th><?= t('field_changed') ?></th>
                    <th><?= t('old_value') ?></th>
                    <th><?= t('new_value') ?></th>
                    <th><?= t('change_date') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (getProfileChanges($_SESSION['user']['id']) as $change): ?>
                    <tr>
                        <td><?= htmlspecialchars($change['field_changed']) ?></td>
                        <td><?= htmlspecialchars($change['old_value']) ?></td>
                        <td><?= htmlspecialchars($change['new_value']) ?></td>
                        <td><?= htmlspecialchars($change['change_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3><?= t('activity_history') ?></h3>
        <table>
            <thead>
                <tr>
                    <th><?= t('activity') ?></th>
                    <th><?= t('date') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT activity, activity_date FROM user_activity WHERE user_id = ? ORDER BY activity_date DESC");
                $stmt->bind_param("i", $_SESSION['user']['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['activity']) ?></td>
                        <td><?= htmlspecialchars($row['activity_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php $stmt->close(); ?>
            </tbody>
        </table>
        <h3><?= t('spending_recommendations') ?></h3>
        <ul>
            <?php foreach ($recommendations as $recommendation): ?>
                <li><?= $recommendation ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
<?php include 'footer.php'; ?>
