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
<main>
    <div class="section">
        <h1><?= t('welcome') ?></h1>
        <p><?= htmlspecialchars($tutorialContent) ?></p>
        <button onclick="window.location.href='index.php'"><?= t('start_using_app') ?></button>
    </div>
</main>
<?php include 'footer.php'; ?>
