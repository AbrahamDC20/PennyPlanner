<?php
require '../controllers/auth.php';
session_start();
requireLogin();
?>

<?php include 'header.php'; ?>
<main style="margin-top: 60px;"> <!-- Adjust margin to match header height -->
    <div class="section">
        <h2><?= t('profile') ?></h2>
        <div class="profile-header">
            <div class="profile-image-container">
                <img src="<?= isset($_SESSION['user']['profile_image']) ? '/Website_Technologies_Abraham/Final_Proyect/uploads/' . htmlspecialchars($_SESSION['user']['profile_image']) : '/Website_Technologies_Abraham/Final_Proyect/assets/default-profile.png' ?>" alt="<?= t('profile_image') ?>" class="profile-image">
            </div>
            <div class="profile-username">
                <h3><?= htmlspecialchars($_SESSION['user']['username']) ?></h3>
            </div>
        </div>
        <form method="POST" action="../routes/update_profile.php" enctype="multipart/form-data">
            <label for="username"><?= t('username') ?>:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($_SESSION['user']['username']) ?>" required>
            <label for="email"><?= t('email') ?>:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" required>
            <label for="phone"><?= t('phone') ?>:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($_SESSION['user']['phone']) ?>">
            <label for="profile_image"><?= t('profile_image') ?>:</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*">
            <p><?= t('image_requirements') ?></p>
            <button type="submit"><?= t('update') ?></button>
        </form>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?>
