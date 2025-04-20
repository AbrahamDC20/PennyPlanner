<?php
require_once '../controllers/auth.php';
require_once '../models/db.php';
session_start();
requireRole('admin');

$roles = getRoles();
$permissions = getPermissions();
?>

<?php include 'header.php'; ?>
<main style="margin-top: 60px;">
    <div class="section">
        <h2><?= t('roles_permissions') ?></h2>
        <h3><?= t('roles') ?></h3>
        <ul>
            <?php foreach ($roles as $role): ?>
                <li><?= htmlspecialchars($role['name']) ?></li>
            <?php endforeach; ?>
        </ul>

        <h3><?= t('permissions') ?></h3>
        <ul>
            <?php foreach ($permissions as $permission): ?>
                <li><?= htmlspecialchars($permission['name']) ?></li>
            <?php endforeach; ?>
        </ul>

        <h3><?= t('assign_permissions') ?></h3>
        <form method="POST" action="../routes/assign_permission.php">
            <label for="role_id"><?= t('select_role') ?>:</label>
            <select name="role_id" id="role_id" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="permission_id"><?= t('select_permission') ?>:</label>
            <select name="permission_id" id="permission_id" required>
                <?php foreach ($permissions as $permission): ?>
                    <option value="<?= $permission['id'] ?>"><?= htmlspecialchars($permission['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit"><?= t('assign') ?></button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
