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
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');
        $firstName = sanitizeInput($_POST['first_name'] ?? '');
        $lastName = sanitizeInput($_POST['last_name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone = sanitizeInput($_POST['phone'] ?? '');

        if (!$email) {
            $error = t('invalid_email');
        } elseif ($username && $password && $confirmPassword && $firstName && $lastName && $phone) {
            if ($password !== $confirmPassword) {
                $error = t('password_mismatch');
            } elseif (strlen($password) < 8) {
                $error = t('weak');
            } else {
                try {
                    $defaultProfileImage = 'Penny_PlannerLogo.png'; // Imagen predeterminada
                    registerUser($username, $password, $firstName, $lastName, $email, $phone, $defaultProfileImage);
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
<main style="margin-top: 80px; overflow-y: auto; max-height: calc(100vh - 80px);">
    <div class="register-container">
        <h2 style="text-align: center;"><?= t('register') ?></h2>
        <form method="POST" id="register-form" class="form-two-columns" style="max-width: 800px; margin: 0 auto;"> <!-- Centrar y ajustar ancho -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div>
                <label for="first_name"><?= t('name') ?>:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div>
                <label for="username"><?= t('username') ?>:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="last_name"><?= t('surname') ?>:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div>
                <label for="password"><?= t('password') ?>:</label>
                <input type="password" id="password" name="password" required>
                <div id="password-strength"></div> <!-- Indicador de fortaleza de contraseña -->
            </div>
            <div>
                <label for="email"><?= t('email') ?>:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="confirm_password"><?= t('confirm_password') ?>:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div>
                <label for="phone"><?= t('phone') ?>:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div style="text-align: center;"> <!-- Botón en la segunda columna -->
                <button type="submit" class="btn-primary" style="width: 100%;"><?= t('register') ?></button>
            </div>
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