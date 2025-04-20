<?php
require_once '../controllers/auth.php';
require_once '../models/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
requireLogin();
$transactions = getTransactions();
?>

<?php include 'header.php'; ?>
<main>
    <div class="section">
        <h2><?= t('transactions') ?></h2>
        <form method="POST" action="transactions.php">
            <div>
                <label for="description"><?= t('description') ?>:</label>
                <input type="text" id="description" name="description" placeholder="<?= t('description') ?>" required>
            </div>
            <div>
                <label for="amount"><?= t('amount') ?>:</label>
                <input type="number" id="amount" name="amount" placeholder="<?= t('amount') ?>" min="0" required>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <label for="currency" style="flex: 0 0 150px;"><?= t('currency_options') ?>:</label>
                <select id="currency" name="currency" style="flex: 1;" required>
                    <option value="USD"><?= t('dollars') ?></option>
                    <option value="EUR"><?= t('euros') ?></option>
                    <option value="GBP"><?= t('pounds') ?></option>
                </select>
                <button type="submit" style="flex: 1;"><?= t('add_transaction') ?></button>
            </div>
        </form>
        <h3><?= t('transaction_history') ?></h3>
        <ul>
            <?php foreach ($transactions as $transaction): ?>
                <li>
                    <?= htmlspecialchars($transaction['description']); ?> - 
                    <?= htmlspecialchars($transaction['amount']); ?> 
                    <?= htmlspecialchars($transaction['currency'] ?? 'USD'); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <div id="chart-container" style="width: 100%; height: 400px;"></div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('chart-container').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['January', 'February', 'March'], // Example data
                    datasets: [{
                        label: 'Spending',
                        data: [500, 700, 300], // Example data
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: $${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>
</main>
<?php include 'footer.php'; ?>
