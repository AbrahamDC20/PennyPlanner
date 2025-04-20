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
<main>
    <div class="admin-panel">
        <h1><?php echo t('admin_panel'); ?></h1>
        <h2><?php echo t('user_management'); ?></h2>
        <table>
            <thead>
                <tr>
                    <th><?php echo t('username'); ?></th>
                    <th><?php echo t('email'); ?></th>
                    <th><?php echo t('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
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
        <h2><?php echo t('currency_options'); ?></h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <select name="currency" style="flex: 1; height: 40px;" required>
                <option value="USD"><?php echo t('dollars'); ?></option>
                <option value="EUR"><?php echo t('euros'); ?></option>
                <option value="GBP"><?php echo t('pounds'); ?></option>
            </select>
            <button type="button" style="flex: 1; height: 40px;"><?php echo t('select_currency'); ?></button>
        </div>
    </div>
</main>
<?php include 'footer.php'; ?>