<?php
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/translations.php'; // Asegúrate de que este archivo existe
session_start();
?>

<?php include 'header.php'; ?>

<main style="margin-top: 0;"> <!-- Ajustar margen superior -->
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
            <p style="text-align: center;">Solo los administradores pueden iniciar sesión desde esta página.</p>
            <button type="submit"><?= t('login') ?></button>
        </form>
        <div style="text-align: center; margin-top: 20px;"> <!-- Centrar enlace -->
            <a href="/Website_Technologies_Abraham/Final_Proyect/views/login.php" style="display: block;"><?= t('login') ?> (<?= t('user') ?>)</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
