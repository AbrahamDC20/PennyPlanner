<?php
require '../controllers/auth.php';
session_start();
requireLogin();
?>

<?php include 'header.php'; ?>
<main style="margin-top: 60px;"> <!-- Adjust margin to match header height -->
    <div class="section">
        <h2><?= t('profile') ?></h2>
        <div class="profile-header" style="display: flex; align-items: center; gap: 20px;"> <!-- Align image and name -->
            <div class="profile-image-container">
                <img src="<?= isset($_SESSION['user']['profile_image']) ? '/Website_Technologies_Abraham/Final_Proyect/uploads/' . htmlspecialchars($_SESSION['user']['profile_image']) : '/Website_Technologies_Abraham/Final_Proyect/assets/default-profile.png' ?>" alt="<?= t('profile_image') ?>" class="profile-image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;"> <!-- Adjusted size and rounded -->
            </div>
            <div class="profile-username">
                <h3 style="margin: 0;"><?= htmlspecialchars($_SESSION['user']['username']) ?></h3>
            </div>
        </div>
        <form method="POST" action="../routes/update_profile.php" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
            <label for="username"><?= t('username') ?>:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($_SESSION['user']['username']) ?>" required>
            <label for="email"><?= t('email') ?>:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" required>
            <label for="phone"><?= t('phone') ?>:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($_SESSION['user']['phone']) ?>">
            <label for="profile_image"><?= t('profile_image') ?>:</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*">
            <button type="submit"><?= t('update') ?></button> <!-- Ensure button is visible -->
        </form>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?>
