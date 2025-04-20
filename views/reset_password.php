<?php
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
require_once dirname(__DIR__) . '/controllers/email.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email) {
        try {
            sendPasswordResetEmail($email);
            $success = t('email_sent');
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = t('email_required');
    }
}
?>
<?php include 'header.php'; ?>
<main>
    <div class="reset-password-container">
        <h1><?php echo t('reset_password'); ?></h1>
        <form method="POST" action="">
            <label for="email"><?php echo t('email'); ?>:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit"><?php echo t('submit'); ?></button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (isset($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?>