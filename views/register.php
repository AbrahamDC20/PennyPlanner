<?php
require_once dirname(__DIR__) . '/controllers/auth.php';
require_once dirname(__DIR__) . '/controllers/translations.php';
session_start();

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = t('invalid_csrf_token');
    } else {
        $_SESSION['language'] = $_POST['language'] ?? 'en'; // Guardar idioma seleccionado
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($username && $password && $confirmPassword && $firstName && $lastName && $email && $phone) {
            if ($password !== $confirmPassword) {
                $error = t('password_mismatch');
            } elseif (strlen($password) < 8) {
                $error = t('weak');
            } else {
                try {
                    registerUser($username, $password, $firstName, $lastName, $email, $phone);
                    header('Location: ../views/tutorial.php'); // Redirigir al tutorial
                    exit();
                } catch (Exception $e) {
                    if ($e->getMessage() === t('email_exists')) {
                        $error = t('email_exists') . ' <a href="../views/reset_password.php">' . t('recover_account') . '</a>';
                    } else {
                        $error = $e->getMessage();
                    }
                }
            }
        } else {
            $error = t('both_fields_required');
        }
    }
}
?>

<?php include 'header.php'; ?>
<main>
    <div class="register-container">
        <h2><?= t('register') ?></h2>
        <form method="POST" id="register-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <label for="language"><?= t('language') ?>:</label>
            <select id="language" name="language" required>
                <option value="en" <?= ($_SESSION['language'] ?? 'en') === 'en' ? 'selected' : '' ?>><?= t('english') ?></option>
                <option value="es" <?= ($_SESSION['language'] ?? 'en') === 'es' ? 'selected' : '' ?>><?= t('spanish') ?></option>
                <option value="lt" <?= ($_SESSION['language'] ?? 'en') === 'lt' ? 'selected' : '' ?>><?= t('lithuanian') ?></option>
            </select>
            <label for="username"><?= t('username') ?>:</label>
            <input type="text" id="username" name="username" required>
            <label for="password"><?= t('password') ?>:</label>
            <input type="password" id="password" name="password" required>
            <label for="confirm_password"><?= t('confirm_password') ?>:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <div id="password-strength" class="password-strength"></div>
            <label for="first_name"><?= t('name') ?>:</label>
            <input type="text" id="first_name" name="first_name" required>
            <label for="last_name"><?= t('surname') ?>:</label>
            <input type="text" id="last_name" name="last_name" required>
            <label for="email"><?= t('email') ?>:</label>
            <input type="email" id="email" name="email" required>
            <label for="phone"><?= t('phone') ?>:</label>
            <input type="text" id="phone" name="phone" required>
            <button type="submit"><?= t('register') ?></button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </div>
</main>
<script>
    // Validación del lado del cliente
    document.getElementById('register-form').addEventListener('submit', function (e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('<?= t('password_mismatch') ?>');
        }
    });

    // Indicador de fortaleza de contraseña
    document.getElementById('password').addEventListener('input', function () {
        const strengthIndicator = document.getElementById('password-strength');
        const password = this.value;
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        const strengthText = ['<?= t('weak') ?>', '<?= t('medium') ?>', '<?= t('strong') ?>'];
        strengthIndicator.textContent = strengthText[Math.min(strength - 1, 2)];
        strengthIndicator.style.color = ['red', 'orange', 'green'][Math.min(strength - 1, 2)];
    });
</script>
<?php include 'footer.php'; ?>