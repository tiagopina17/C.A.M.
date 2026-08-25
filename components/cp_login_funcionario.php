<!-- Shared market/carimbo auth theme lives in meusestilos.css
     (.auth-page-wrapper, .badge-stamp, .sam override, .auth-card,
     .auth-title, .auth-subtitle, .form-label-stamp, .form-control-mkt,
     .input-group-mkt, .toggle-pw-btn, .btn-mkt-submit, .auth-alert,
     .auth-link-line/.auth-link). Only this page's one-off tweaks stay here. -->
<style>
.auth-title{ font-size: clamp(1.5rem, 3vw, 1.95rem); line-height: 1.15; }

.auth-alert.alert-danger p{ margin-bottom: .35rem; }
.auth-alert.alert-danger p:last-child{ margin-bottom: 0; }
</style>

<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="text-center">
            <span class="badge-stamp"><i class="fas fa-user-tie"></i>Selo · área reservada</span>
        </div>
        <h2 class="auth-title">Iniciar Sessão <span class="sam">Funcionários</span></h2>
        <p class="auth-subtitle">Acede à tua conta inserindo os teus dados abaixo.</p>

        <?php
        // FIXED: Handle both single error and array of errors
        if (isset($_SESSION['login_error'])) {
            echo '<div class="auth-alert alert-danger mb-3">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
            unset($_SESSION['login_error']);
        }

        // Handle login_errors array
        if (isset($_SESSION['login_errors']) && !empty($_SESSION['login_errors'])) {
            echo '<div class="auth-alert alert-danger mb-3">';
            foreach($_SESSION['login_errors'] as $error) {
                echo '<p class="mb-1">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
            unset($_SESSION['login_errors']);
        }

        // Display success message if any
        if (isset($_SESSION['success_message'])) {
            echo '<div class="auth-alert alert-success mb-3">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <form action="./scripts/sc_login_funcionario.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label-stamp">Email</label>
                <input type="email" class="form-control form-control-mkt" id="email" placeholder="Insira o seu email"
                       name="email" value="<?php echo isset($_SESSION['login_email']) ? htmlspecialchars($_SESSION['login_email']) : ''; ?>" required>
                <div class="valid-feedback">Tudo certo!</div>
                <div class="invalid-feedback">Este campo é obrigatório.</div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label-stamp">Password</label>
                <div class="input-group-mkt">
                    <input type="password" class="form-control form-control-mkt-flat" id="password" placeholder="Insira a sua password" name="password" required>
                    <button type="button" class="toggle-pw-btn" id="togglePassword">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                <div class="valid-feedback">Tudo certo!</div>
                <div class="invalid-feedback">Este campo é obrigatório.</div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn-mkt-submit">Entrar</button>
            </div>
        </form>

        <div class="auth-link-line">
            <a href="./login.php" class="auth-link">Não é funcionário ou proprietário de uma loja? Clique aqui para entrar</a>
        </div>
    </div>
</div>

<?php
// Clean up the email session variable after displaying
if (isset($_SESSION['login_email'])) {
    unset($_SESSION['login_email']);
}
?>

<!-- Add Font Awesome for icons if not already included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle the eye icon
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
});
</script>