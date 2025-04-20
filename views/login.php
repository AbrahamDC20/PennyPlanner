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
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"> <!-- Evitar error -->
            <h2><?= t('login') ?></h2>
            <label for="username"><?= t('username') ?>:</label>
            <input type="text" id="username" name="username" required>
            <label for="password"><?= t('password') ?>:</label>
            <input type="password" id="password" name="password" required>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <button type="submit" style="width: 100%;"><?= t('login') ?></button> <!-- Botón ajustado -->
        </form>
        <div style="text-align: center; margin-top: 10px;"> <!-- Centrar enlaces -->
            <p><a href="/Website_Technologies_Abraham/Final_Proyect/views/register.php"><?= t('register') ?></a></p>
            <p><a href="/Website_Technologies_Abraham/Final_Proyect/views/admin_login.php"><?= t('login') ?> (<?= t('admin') ?>)</a></p>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>