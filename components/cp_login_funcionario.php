<div class="container main-content d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 120px);">
    <div class="col-md-8 mx-auto">
        <div class="card shadow p-4">
            <h2 class="text-center sam mb-4">Iniciar Sessão - Funcionários</h2>
            <p class="text-center text-muted mb-4">Acede à tua conta inserindo os teus dados abaixo.</p>

            <?php
            // FIXED: Handle both single error and array of errors
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            
            // Handle login_errors array
            if (isset($_SESSION['login_errors']) && !empty($_SESSION['login_errors'])) {
                echo '<div class="alert alert-danger">';
                foreach($_SESSION['login_errors'] as $error) {
                    echo '<p class="mb-1">' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['login_errors']);
            }

            // Display success message if any
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>

            <form action="./scripts/sc_login_funcionario.php" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" placeholder="Insira o seu email" 
                           name="email" value="<?php echo isset($_SESSION['login_email']) ? htmlspecialchars($_SESSION['login_email']) : ''; ?>" required>
                    <div class="valid-feedback">Tudo certo!</div>
                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" placeholder="Insira a sua password" name="password" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <div class="valid-feedback">Tudo certo!</div>
                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-verde">Entrar</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="./login.php" class="fw-semibold fs-6 ver-mais-link">Não é funcionário ou proprietário de uma loja? Clique aqui para entrar</a>
            </div>
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

