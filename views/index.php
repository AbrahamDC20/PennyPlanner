<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
requireLogin();
$userId = $_SESSION['user']['id']; // Obtén el ID del usuario actual
$transactions = getTransactions($userId); // Pasa el userId a la función
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
        <div id="chart-container" style="width: 100%; height: 400px;"></div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('chart-container').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($transactions, 'description')) ?>,
                    datasets: [{
                        label: '<?= t('amount') ?>',
                        data: <?= json_encode(array_column($transactions, 'amount')) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
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