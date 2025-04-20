<?php
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
session_start();
requireRole('admin');

$users = listUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    deleteUser($_POST['delete_user_id']);
    header('Location: admin.php');
    exit();
}

$adminId = $_SESSION['user']['id'];
$friends = getFriends($adminId); // Obtener amigos del administrador
?>
<?php include 'header.php'; ?>
<main style="margin-top: 0;"> <!-- Ajustar margen superior -->
    <div class="admin-panel">
        <h1><?php echo t('admin_panel'); ?></h1>
        <h2><?php echo t('user_management'); ?></h2>
        <table>
            <thead>
                <tr>
                    <th><?php echo t('username'); ?></th>
                    <th><?php echo t('email'); ?></th>
                    <th><?php echo t('actions'); ?></th> <!-- Traducir -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td> <!-- Mostrar correo electrónico -->
                        <td>
                            <form method="POST">
                                <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit"><?php echo t('delete'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2><?php echo t('global_statistics'); ?></h2>
        <p><?php echo t('total_users'); ?>: <?php echo count($users); ?></p>
        <p><?php echo t('total_transactions'); ?>: 
            <?php
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM transactions");
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            echo $result['total'];
            $stmt->close();
            ?>
        </p>
        <div id="chart-container" style="width: 100%; height: 400px;"></div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('chart-container').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['<?= t('total_users') ?>'],
                    datasets: [{
                        label: '<?= t('statistics') ?>',
                        data: [<?= count($users) ?>],
                        backgroundColor: ['#007bff']
                    }]
                }
            });
        </script>
        <!-- Agregar más estadísticas aquí -->
    </div>
    <ul>
        <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/roles_permissions.php"><?= t('roles_permissions') ?></a></li>
    </ul>
</main>
<?php include 'footer.php'; ?>