<?php
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
?>

<?php include 'header.php'; ?>

<main style="margin-top: 80px;"> <!-- Ajustar margen superior -->
    <div class="login-container">
        <form method="post" action="../routes/login.php" class="login-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"> <!-- Validación CSRF -->
            <h2><?= t('login') ?></h2>
            <label for="username"><?= t('username') ?>:</label>
            <input type="text" id="username" name="username" required>
            <label for="password"><?= t('password') ?>:</label>
            <input type="password" id="password" name="password" required>
            <?php if ($_SESSION['user']['2fa_enabled'] ?? false): ?>
                <label for="2fa_code"><?= t('2fa_code') ?>:</label>
                <input type="text" id="2fa_code" name="2fa_code" required>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <button type="submit" style="width: 100%;"><?= t('login') ?></button> <!-- Botón ajustado -->
        </form>
        <div style="text-align: center; margin-top: 20px;"> <!-- Centrar enlaces -->
            <a href="/Website_Technologies_Abraham/Final_Proyect/views/register.php" style="display: block;"><?= t('register') ?></a>
            <a href="/Website_Technologies_Abraham/Final_Proyect/views/admin_login.php" style="display: block;"><?= t('login') ?> (<?= t('admin') ?>)</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>