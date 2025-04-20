<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
requireLogin();
$transactions = getTransactions(); // Ensure $transactions is defined
?>
<?php include 'header.php'; ?>
<main style="margin-top: 60px;"> <!-- Adjust margin to match header height -->
    <div class="section">
        <h2><?= t('transactions') ?></h2>
        <h3><?= t('transaction_history') ?></h3>
        <ul>
            <?php foreach ($transactions as $transaction): ?>
                <li><?php echo htmlspecialchars($transaction['description']); ?> - <?php echo htmlspecialchars($transaction['amount']); ?> USD</li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
<?php include 'footer.php'; ?>