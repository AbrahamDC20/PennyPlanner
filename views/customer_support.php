<?php include 'header.php'; ?>
<main style="margin-top: 60px;">
    <div class="section" style="max-width: 800px; margin: 0 auto;">
        <h2 style="color: #007bff; text-align: center;"><?= t('customer_support') ?> - Piggy</h2>
        <div id="chat-container" style="margin-top: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9; height: 400px; overflow-y: auto;">
            <?php if (isset($_SESSION['chat_history'])): ?>
                <?php foreach ($_SESSION['chat_history'] as $message): ?>
                    <div style="margin-bottom: 15px;">
                        <strong style="color: <?= $message['sender'] === 'user' ? '#007bff' : '#28a745'; ?>;">
                            <?= $message['sender'] === 'user' ? 'You' : 'Piggy' ?>:
                        </strong>
                        <p style="margin: 5px 0;"><?= htmlspecialchars($message['text']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <form method="POST" action="../routes/ask_ai.php" style="margin-top: 20px; display: flex; gap: 10px;">
            <input type="text" name="question" placeholder="<?= t('your_question') ?>" style="flex: 1; padding: 10px;" required>
            <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;"><?= t('submit') ?></button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
