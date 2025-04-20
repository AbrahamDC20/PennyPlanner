<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
require_once dirname(__DIR__) . '/controllers/translations.php';

// Ensure default language is set to avoid warnings
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'es'; // Default to Spanish
}

function renderLanguageOptions($currentLanguage) {
    $languages = [
        'es' => ['Español', '/Website_Technologies_Abraham/Final_Proyect/images/España.jpeg'],
        'en' => ['English', '/Website_Technologies_Abraham/Final_Proyect/images/UK.png'],
        'lt' => ['Lietuvių', '/Website_Technologies_Abraham/Final_Proyect/images/Lituania.png']
    ];

    foreach ($languages as $code => [$label, $flag]) {
        echo '<li>
            <form method="POST" action="/Website_Technologies_Abraham/Final_Proyect/routes/set_language.php">
                <button type="submit" name="language" value="' . htmlspecialchars($code) . '" class="dropdown-item">
                    <img src="' . htmlspecialchars($flag) . '" alt="' . htmlspecialchars($label) . '" class="flag-icon">
                    ' . htmlspecialchars($label) . '
                </button>
            </form>
        </li>';
    }
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
    <div id="notification" class="notification"></div>
    <header>
        <nav>
            <ul class="menu" role="menu">
                <li role="menuitem"><a href="/Website_Technologies_Abraham/Final_Proyect/views/index.php"><?= t('home') ?></a></li>
                <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/transactions.php"><?= t('transactions') ?></a></li>
                <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/settings.php"><?= t('settings') ?></a></li>
                <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/friends_management.php"><?= t('friends') ?></a></li>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <li><a href="/Website_Technologies_Abraham/Final_Proyect/views/admin.php"><?= t('admin_panel') ?></a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user'])): ?>
                    <li style="margin-right: 20px;">
                        <a href="/Website_Technologies_Abraham/Final_Proyect/views/profile.php" class="user-menu-button" style="display: flex; align-items: center; gap: 10px;">
                            <img src="<?= isset($_SESSION['user']['profile_image']) ? '/Website_Technologies_Abraham/Final_Proyect/uploads/' . htmlspecialchars($_SESSION['user']['profile_image']) : '/Website_Technologies_Abraham/Final_Proyect/assets/default-profile.png' ?>" alt="<?= t('profile_image') ?>" class="header-profile-image">
                            <?= htmlspecialchars($_SESSION['user']['username']) ?>
                        </a>
                    </li>
                    <li style="margin-left: auto; display: flex; align-items: center;">
                        <form method="POST" action="/Website_Technologies_Abraham/Final_Proyect/routes/logout.php">
                            <button type="submit" class="logout-button"><?= t('logout') ?></button>
                        </form>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
    </main>
    <footer class="footer">
    </footer>
    <script>
        function toggleDropdown(menuId) {
            const menu = document.getElementById(menuId);
            const isActive = menu.classList.contains('active');
            document.querySelectorAll('.dropdown-menu').forEach((dropdown) => {
                dropdown.classList.remove('active');
                dropdown.setAttribute('aria-expanded', 'false');
            });
            if (!isActive) {
                menu.classList.add('active');
                menu.setAttribute('aria-expanded', 'true');
            }
        }

        // Cierra el menú al hacer clic fuera de él
        document.addEventListener('click', (event) => {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach((dropdown) => {
                const toggleButton = dropdown.previousElementSibling;
                if (!dropdown.contains(event.target) && !toggleButton.contains(event.target)) {
                    dropdown.classList.remove('active');
                    dropdown.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Cierra el menú al seleccionar una opción
        document.querySelectorAll('.dropdown-menu button').forEach((button) => {
            button.addEventListener('click', () => {
                const menu = button.closest('.dropdown-menu');
                menu.classList.remove('active');
                menu.setAttribute('aria-expanded', 'false');
            });
        });
    </script>
    <script>
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

        // Asistente interactivo
        function showAssistant() {
            alert('<?= t('welcome') ?>! <?= t('tutorial_home') ?>');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            document.querySelector('header').classList.toggle('dark-mode');
            document.querySelector('footer').classList.toggle('dark-mode');
        }
    </script>
    <script src="/Website_Technologies_Abraham/Final_Proyect/assets/socket.io.js"></script>
    <script>
        const socket = io('http://localhost:3000'); // Conexión a WebSocket

        socket.on('notification', (data) => {
            showNotification(data.message, data.type);
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/Website_Technologies_Abraham/Final_Proyect/service-worker.js')
                .then(() => console.log('Service Worker registered successfully.'))
                .catch((error) => console.error('Service Worker registration failed:', error));
        }
    </script>
</body>
</html>
