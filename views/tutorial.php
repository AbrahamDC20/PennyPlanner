<?php
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/tutorialController.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();
$currentLanguage = $_SESSION['language'] ?? 'en'; // Usar el idioma seleccionado
$tutorialContent = getTutorial($_SESSION['user']['id']);
?>
<?php include 'header.php'; ?>
<main style="margin-top: 60px;">
    <div class="section">
        <h2><?= t('tutorial') ?></h2>
        <p><?= t('tutorial_description') ?></p>
    </div>
</main>
<?php include 'footer.php'; ?>
