<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
require_once dirname(__DIR__) . '/controllers/translations.php';

// Ensure default language is set to avoid warnings
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'es'; // Default to Spanish
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['language'] ?? 'es') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('home') ?> - PennyPlanner</title>
    <link rel="stylesheet" href="/Website_Technologies_Abraham/Final_Proyect/assets/styles.css">
</head>
<body>
    <div id="loading-indicator" style="display: none;">Loading...</div> <!-- Loading spinner -->
    <div id="notification" class="notification" style="display: none;"></div> <!-- Notification system -->
    <header>
        <nav>
            <ul class="menu">
                <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/index.php"><?= t('home') ?></a></li>
                <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/transactions.php"><?= t('transactions') ?></a></li>
                <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/settings.php"><?= t('settings') ?></a></li>
                <!-- Removed documentation link -->
                <?php if (isset($_SESSION['user'])): ?>
                    <li class="dropdown" style="margin-right: 20px;">
                        <button class="dropdown-toggle user-menu-button" onclick="toggleDropdown('user-menu')">
                            <img src="<?= isset($_SESSION['user']['profile_image']) ? '/Website_Technologies_Abraham/Final_Proyect/uploads/' . htmlspecialchars($_SESSION['user']['profile_image']) : '/Website_Technologies_Abraham/Final_Proyect/assets/default-profile.png' ?>" alt="<?= t('profile_image') ?>" class="header-profile-image">
                            <?= htmlspecialchars($_SESSION['user']['username']) ?>
                        </button>
                        <ul id="user-menu" class="dropdown-menu">
                            <li>
                                <label class="switch" for="dark-mode-toggle">
                                    <?= t('dark_mode') ?>
                                    <input type="checkbox" id="dark-mode-toggle" style="display: none;">
                                    <span class="slider"></span>
                                </label>
                            </li>
                            <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/profile.php"><?= t('profile') ?></a></li>
                            <li class="dropdown">
                                <button class="dropdown-toggle user-menu-button" onclick="toggleDropdown('language-menu')">
                                    <?= t('language') ?>
                                </button>
                                <ul id="language-menu" class="dropdown-menu">
                                    <?php if ($_SESSION['language'] !== 'es'): ?>
                                        <li>
                                            <form method="POST" action="/Website_Technologies_Abraham/Final_Proyect/routes/set_language.php">
                                                <button type="submit" name="language" value="es" class="dropdown-item">
                                                    <img src="/Website_Technologies_Abraham/Final_Proyect/images/España.jpeg" alt="Español" class="flag-icon">
                                                    <?= t('spanish') ?>
                                                </button>
                                            </form>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($_SESSION['language'] !== 'en'): ?>
                                        <li>
                                            <form method="POST" action="/Website_Technologies_Abraham/Final_Proyect/routes/set_language.php">
                                                <button type="submit" name="language" value="en" class="dropdown-item">
                                                    <img src="/Website_Technologies_Abraham/Final_Proyect/images/UK.png" alt="English" class="flag-icon">
                                                    <?= t('english') ?>
                                                </button>
                                            </form>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($_SESSION['language'] !== 'lt'): ?>
                                        <li>
                                            <form method="POST" action="/Website_Technologies_Abraham/Final_Proyect/routes/set_language.php">
                                                <button type="submit" name="language" value="lt" class="dropdown-item">
                                                    <img src="/Website_Technologies_Abraham/Final_Proyect/images/Lituania.png" alt="Lituano" class="flag-icon">
                                                    <?= t('lithuanian') ?>
                                                </button>
                                            </form>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                            <li><a href="/Website_Technologies_Abraham/Final_Proyect/routes/logout.php"><?= t('logout') ?></a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="dropdown" style="margin-right: 20px;">
                        <button class="dropdown-toggle user-menu-button" onclick="toggleDropdown('language-menu')">
                            <?= t('language') ?>
                        </button>
                        <ul id="language-menu" class="dropdown-menu">
                            <?php if ($_SESSION['language'] !== 'es'): ?>
                                <li>
                                    <form method="POST" action="/Website_Technologies_Abraham/Final_Proyect/routes/set_language.php">
                                        <button type="submit" name="language" value="es" class="dropdown-item">
                                            <img src="/Website_Technologies_Abraham/Final_Proyect/images/España.jpeg" alt="Español" class="flag-icon">
                                            <?= t('spanish') ?>
                                        </button>
                                    </form>
                                </li>
                            <?php endif; ?>
                            <?php if ($_SESSION['language'] !== 'en'): ?>
                                <li>
                                    <form method="POST" action="/Website_Technologies_Abraham/Final_Proyect/routes/set_language.php">
                                        <button type="submit" name="language" value="en" class="dropdown-item">
                                            <img src="/Website_Technologies_Abraham/Final_Proyect/images/UK.png" alt="English" class="flag-icon">
                                            <?= t('english') ?>
                                        </button>
                                    </form>
                                </li>
                            <?php endif; ?>
                            <?php if ($_SESSION['language'] !== 'lt'): ?>
                                <li>
                                    <form method="POST" action="/Website_Technologies_Abraham/Final_Proyect/routes/set_language.php">
                                        <button type="submit" name="language" value="lt" class="dropdown-item">
                                            <img src="/Website_Technologies_Abraham/Final_Proyect/images/Lituania.png" alt="Lituano" class="flag-icon">
                                            <?= t('lithuanian') ?>
                                        </button>
                                    </form>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
    </main>
    <footer class="footer">
        <p>&copy; 2025 PennyPlanner. <?= t('all_rights_reserved') ?></p>
    </footer>
    <script>
        function toggleDropdown(menuId) {
            const menu = document.getElementById(menuId);
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        // Notification system
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            setTimeout(() => notification.style.display = 'none', 3000);
        }

        // Loading spinner
        function showLoading() {
            document.getElementById('loading-indicator').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loading-indicator').style.display = 'none';
        }

        // Dark mode toggle
        const toggle = document.getElementById('dark-mode-toggle');
        const body = document.body;

        if (localStorage.getItem('dark-mode') === 'enabled') {
            body.classList.add('dark-mode');
            toggle.checked = true;
        }

        toggle?.addEventListener('change', () => {
            if (toggle.checked) {
                body.classList.add('dark-mode');
                localStorage.setItem('dark-mode', 'enabled');
            } else {
                body.classList.remove('dark-mode');
                localStorage.setItem('dark-mode', 'disabled');
            }
        });
    </script>
</body>
</html>
