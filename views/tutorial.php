<?php
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/tutorialController.php';
require_once dirname(__DIR__) . '/controllers/aiController.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();
$currentLanguage = $_SESSION['language'] ?? 'en'; // Usar el idioma seleccionado
$tutorialContent = generatePersonalizedTutorial($_SESSION['user']['id']);
?>
<?php include 'header.php'; ?>
<main style="margin-top: 60px;">
    <div class="section" style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h2 style="color: #007bff;"><?= t('tutorial') ?> - Piggy</h2>
        <p style="font-size: 1.2em;"><?= t('tutorial_description') ?></p>
        <div style="margin-top: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9; text-align: left;">
            <?= nl2br(htmlspecialchars($tutorialContent)) ?>
        </div>
    </div>
</main>
<?php include 'footer.php'; ?>
