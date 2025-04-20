<?php
require '../controllers/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
requireLogin();
?>

<?php include 'header.php'; ?>
<main style="margin-top: 60px;"> <!-- Adjust margin to match header height -->
    <div class="section">
        <h2><?= t('settings') ?></h2>
        <form method="POST" action="../routes/settings.php">
            <label for="response_mode"><?= t('ai_response_mode') ?>:</label>
            <select name="response_mode" id="response_mode">
                <option value="friendly" <?= ($_SESSION['response_mode'] ?? 'friendly') === 'friendly' ? 'selected' : '' ?>><?= t('friendly') ?></option>
                <option value="formal" <?= ($_SESSION['response_mode'] ?? 'friendly') === 'formal' ? 'selected' : '' ?>><?= t('formal') ?></option>
            </select>
            <label for="theme"><?= t('theme') ?>:</label>
            <select name="theme" id="theme">
                <option value="light" <?= ($_SESSION['theme'] ?? 'light') === 'light' ? 'selected' : '' ?>><?= t('light') ?></option>
                <option value="dark" <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? 'selected' : '' ?>><?= t('dark') ?></option>
            </select>
            <button type="submit" name="update_settings"><?= t('update') ?></button>
        </form>
        <p><?= ($_SESSION['response_mode'] ?? 'friendly') === 'friendly' ? t('friendly_mode_message') : t('formal_mode_message') ?></p>
    </div>
</main>
<?php include 'footer.php'; ?>
