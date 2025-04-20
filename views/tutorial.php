<?php
require_once dirname(__DIR__) . '/controllers/translations.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentLanguage = $_SESSION['language'] ?? 'en'; // Usar el idioma seleccionado
?>
<?php include 'header.php'; ?>
<main>
    <div class="section">
        <h1><?= t('welcome') ?></h1>
        <p><?= t('select_option') ?></p>
        <ol>
            <li><?= t('tutorial_home') ?></li>
            <li><?= t('tutorial_profile') ?></li>
            <li><?= t('tutorial_transactions') ?></li>
            <li><?= t('tutorial_settings') ?></li>
        </ol>
        <button onclick="window.location.href='index.php'"><?= t('start_using_app') ?></button>
    </div>
</main>
<?php include 'footer.php'; ?>
