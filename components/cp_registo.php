<!-- Shared market/carimbo auth theme lives in meusestilos.css
     (.auth-page-wrapper, .badge-stamp, .sam override, .auth-card,
     .auth-title, .auth-subtitle, .form-label-stamp, .form-control-mkt,
     .input-group-mkt, .toggle-pw-btn, .btn-mkt-submit, .auth-alert,
     .auth-link-line/.auth-link). Only this page's one-off tweak stays here. -->
<style>
.auth-alert.alert-danger ul{ padding-left: 1.2rem; }
</style>

<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="text-center">
            <span class="badge-stamp"><i class="fas fa-user-plus"></i>Selo · nova conta</span>
        </div>
        <h2 class="auth-title">Cria a tua <span class="sam">Conta</span></h2>
        <p class="auth-subtitle">Preenche o formulário abaixo para te registares na plataforma.</p>

        <?php
        // Get form data if exists
        $form_nome = '';
        $form_email = '';
        if (isset($_SESSION['form_data'])) {
            $form_nome = htmlspecialchars($_SESSION['form_data']['nome']);
            $form_email = htmlspecialchars($_SESSION['form_data']['email']);
        }

        if (isset($_SESSION['registration_errors'])) {
            echo '<div class="auth-alert alert-danger mb-3"><strong>Erros encontrados:</strong><ul class="mb-0 mt-2">';
            foreach ($_SESSION['registration_errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
            unset($_SESSION['registration_errors']);
        }
        if (isset($_SESSION['success_message'])) {
            echo '<div class="auth-alert alert-success mb-3">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
            // Clear form data on success
            unset($_SESSION['form_data']);
        }
        ?>

        <form action="./scripts/sc_registo.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="nome" class="form-label-stamp">Nome</label>
                <input type="text" class="form-control form-control-mkt" id="nome" placeholder="Insira o seu nome" name="nome" value="<?php echo $form_nome; ?>" required>
                <div class="valid-feedback">Tudo certo!</div>
                <div class="invalid-feedback">Este campo é obrigatório.</div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label-stamp">Email</label>
                <input type="email" class="form-control form-control-mkt" id="email" placeholder="Insira o seu email" name="email" value="<?php echo $form_email; ?>" required>
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
                <button type="submit" class="btn-mkt-submit">Registar</button>
            </div>
        </form>
    </div>
</div>

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