<?php
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
?>

<?php include 'header.php'; ?>

<main>
    <div class="login-container">
        <form method="post" action="../routes/login.php" class="login-form">
            <h2><?= t('login') ?></h2>
            <label for="username"><?= t('username') ?>:</label>
            <input type="text" id="username" name="username" required>
            <label for="password"><?= t('password') ?>:</label>
            <input type="password" id="password" name="password" required>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <button type="submit"><?= t('login') ?></button>
        </form>
        <p><a href="/Website_Technologies_Abraham/Final_Proyect/views/register.php"><?= t('register') ?></a></p>
        <p><a href="/Website_Technologies_Abraham/Final_Proyect/views/admin_login.php"><?= t('login') ?> (<?= t('admin') ?>)</a></p>
    </div>
</main>

<?php include 'footer.php'; ?>