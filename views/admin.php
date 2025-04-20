<?php
require_once dirname(__DIR__) . '/models/db.php';
require_once dirname(__DIR__) . '/controllers/auth.php';
session_start();
requireRole('admin');

$users = listUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    deleteUser($_POST['delete_user_id']);
    header('Location: admin.php');
    exit();
}
?>
<?php include 'header.php'; ?>
<main style="margin: 80px auto; max-width: 1200px;"> <!-- Márgenes laterales -->
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
                        <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td> <!-- Mostrar correo -->
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
        <!-- Agregar más estadísticas aquí -->
    </div>
</main>
<?php include 'footer.php'; ?>