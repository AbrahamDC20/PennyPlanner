<?php
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
session_start();
?>

<?php include 'header.php'; ?>

<main>
    <div class="login-container">
        <form method="post" action="../routes/admin_login.php" class="login-form">
            <h2><?= t('login') ?> (<?= t('admin') ?>)</h2>
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
        <p><a href="/Website_Technologies_Abraham/Final_Proyect/views/login.php"><?= t('login') ?> (<?= t('user') ?>)</a></p>
    </div>
</main>

<?php include 'footer.php'; ?>
