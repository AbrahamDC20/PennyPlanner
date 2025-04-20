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
<main style="margin: 80px auto; max-width: 800px; padding: 20px;">
    <div class="section">
        <h2 style="text-align: center;"><?= t('transactions') ?></h2>
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
        <form id="report-form" method="GET" action="../routes/generate_report.php" style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 20px;">
            <div>
                <label for="start_date"><?= t('start_date') ?>:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            <div>
                <label for="end_date"><?= t('end_date') ?>:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
            <button type="button" id="generate-report" class="btn-primary"><?= t('generate_report') ?></button>
        </form>
        <div id="report-results" style="margin-top: 20px;">
            <!-- Aquí se mostrarán los resultados del reporte -->
        </div>
        <script>
            document.getElementById('generate-report').addEventListener('click', function () {
                const form = document.getElementById('report-form');
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);

                fetch(form.action + '?' + params.toString())
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const resultsContainer = document.getElementById('report-results');
                        resultsContainer.innerHTML = '';

                        if (data.error) {
                            resultsContainer.textContent = data.error;
                        } else if (data.length === 0) {
                            resultsContainer.textContent = '<?= t('no_transactions_found') ?>';
                        } else {
                            const table = document.createElement('table');
                            table.innerHTML = `
                                <thead>
                                    <tr>
                                        <th><?= t('description') ?></th>
                                        <th><?= t('amount') ?></th>
                                        <th><?= t('currency') ?></th>
                                        <th><?= t('date') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(transaction => `
                                        <tr>
                                            <td>${transaction.description}</td>
                                            <td>${transaction.amount}</td>
                                            <td>${transaction.currency}</td>
                                            <td>${transaction.date}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            `;
                            resultsContainer.appendChild(table);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching report:', error);
                        document.getElementById('report-results').textContent = '<?= t('error_loading_report') ?>';
                    });
            });
        </script>
        <h3 style="text-align: center;"><?= t('transaction_history') ?></h3>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($transactions as $transaction): ?>
                <li style="margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <?= htmlspecialchars($transaction['description']); ?> - 
                    <?= htmlspecialchars($transaction['amount']); ?> 
                    <?= htmlspecialchars($transaction['currency'] ?? 'USD'); ?>
                    <form method="POST" action="../routes/toggle_favorite.php" style="display: inline;">
                        <input type="hidden" name="transaction_id" value="<?= $transaction['id'] ?>">
                        <button type="submit" class="btn-secondary"><?= t('favorite') ?></button>
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
