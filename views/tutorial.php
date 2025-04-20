<?php
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/tutorialController.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();
$tutorialSteps = generateTutorial(); // Generar pasos dinámicos
?>
<?php include 'header.php'; ?>
<main style="margin-top: 60px;">
    <div class="section" style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h2 style="color: #007bff;"><?= t('tutorial') ?> - PennyPlanner</h2>
        <p style="font-size: 1.2em;"><?= t('tutorial_description') ?></p>
        <button id="start-tutorial" style="margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <?= t('start_tutorial') ?>
        </button>
    </div>
</main>
<script>
    const tutorialSteps = <?= json_encode($tutorialSteps) ?>;
</script>
<script src="../assets/tutorial.js"></script>
<?php include 'footer.php'; ?>
