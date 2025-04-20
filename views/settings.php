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
        <form method="POST" action="../routes/set_language.php"> <!-- Cambiar acción al archivo correcto -->
            <h3><?= t('language') ?></h3>
            <div style="display: flex; gap: 20px; align-items: center;">
                <button type="submit" name="language" value="es" class="language-button" style="color: black;">
                    <img src="/Website_Technologies_Abraham/Final_Proyect/images/España.jpeg" alt="Español" class="flag-icon">
                    <?= t('spanish') ?>
                </button>
                <button type="submit" name="language" value="en" class="language-button" style="color: black;">
                    <img src="/Website_Technologies_Abraham/Final_Proyect/images/UK.png" alt="English" class="flag-icon">
                    <?= t('english') ?>
                </button>
                <button type="submit" name="language" value="lt" class="language-button" style="color: black;">
                    <img src="/Website_Technologies_Abraham/Final_Proyect/images/Lituania.png" alt="Lietuvių" class="flag-icon">
                    <?= t('lithuanian') ?>
                </button>
            </div>
        </form>
        <form method="POST" action="../routes/settings.php">
            <!-- Configuración de modo de respuesta de IA -->
            <div style="margin-top: 20px;"> <!-- Agregar margen superior -->
                <label for="response_mode"><?= t('ai_response_mode') ?></label>
                <select name="response_mode" id="response_mode">
                    <option value="friendly" <?= ($_SESSION['response_mode'] ?? 'friendly') === 'friendly' ? 'selected' : '' ?>><?= t('friendly') ?></option>
                    <option value="formal" <?= ($_SESSION['response_mode'] ?? 'friendly') === 'formal' ? 'selected' : '' ?>><?= t('formal') ?></option>
                </select>
            </div>

            <!-- Configuración de notificaciones -->
            <div style="display: flex; align-items: center; gap: 10px;">
                <label for="notifications"><?= t('notifications') ?></label>
                <div class="switch">
                    <input type="checkbox" id="notifications" name="notifications" value="enabled" <?= ($_SESSION['notifications'] ?? 'enabled') === 'enabled' ? 'checked' : '' ?>>
                    <label for="notifications"></label>
                </div>
            </div>

            <button type="submit" name="update_settings"><?= t('update') ?></button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
