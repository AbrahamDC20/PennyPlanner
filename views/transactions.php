<?php
require_once '../controllers/auth.php';
require_once '../models/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
requireLogin();
$userId = $_SESSION['user']['id']; // Obtén el ID del usuario actual

$page = $_GET['page'] ?? 1; // Definir la variable $page
$limit = 10; // Definir la variable $limit
$offset = ($page - 1) * $limit;

$transactions = getTransactions($userId, $limit, $offset); // Pasa el userId a la función
?>

<?php include 'header.php'; ?>
<main style="margin-top: 0;"> <!-- Ajustar margen superior -->
    <div class="section">
        <h2><?= t('transactions') ?></h2>
        <form method="POST" action="../routes/add_transaction.php" class="styled-form"> <!-- Corregir acción -->
            <div class="form-group">
                <label for="description"><?= t('description') ?>:</label>
                <input type="text" id="description" name="description" placeholder="<?= t('description') ?>" required>
            </div>
            <div class="form-group">
                <label for="amount"><?= t('amount') ?>:</label>
                <input type="number" id="amount" name="amount" placeholder="<?= t('amount') ?>" min="0" required>
            </div>
            <div class="form-group-inline">
                <label for="currency"><?= t('currency_options') ?>:</label>
                <select id="currency" name="currency" required>
                    <option value="USD"><?= t('dollars') ?></option>
                    <option value="EUR"><?= t('euros') ?></option>
                    <option value="GBP"><?= t('pounds') ?></option>
                </select>
                <button type="submit" class="btn-primary"><?= t('add_transaction') ?></button>
            </div>
        </form>
        <h3 style="text-align: center;"><?= t('transaction_history') ?></h3>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($transactions as $transaction): ?>
                <li style="margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <?= htmlspecialchars($transaction['description']); ?> - 
                    <?= htmlspecialchars($transaction['amount']); ?> 
                    <?= htmlspecialchars($transaction['currency'] ?? 'USD'); ?>
                    <form method="POST" action="../routes/delete_transaction.php" style="display: inline;">
                        <input type="hidden" name="transaction_id" value="<?= $transaction['id']; ?>">
                        <button type="submit" class="btn-danger" style="margin-left: 10px;"><?= t('delete') ?></button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="pagination" style="text-align: center; margin-top: 20px;">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn-secondary"><?= t('previous') ?></a>
            <?php endif; ?>
            <span><?= t('page') ?> <?= $page ?></span>
            <?php if (count($transactions) === $limit): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn-secondary"><?= t('next') ?></a>
            <?php endif; ?>
        </div>
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
