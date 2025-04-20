<?php
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if ($token && $newPassword) {
        try {
            resetPassword($token, $newPassword);
            $success = t('password_changed');
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = t('both_fields_required');
    }
}
?>
<?php include 'header.php'; ?>
<main>
    <div class="reset-password-container">
        <h1><?= t('reset_password') ?></h1>
        <form method="POST" action="">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            <label for="new_password"><?= t('password') ?>:</label>
            <input type="password" id="new_password" name="new_password" required>
            <button type="submit"><?= t('submit') ?></button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (isset($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?>
