<div class="container mt-5 mb-5 main-content" >
    <div class="col-md-8 mx-auto">
        <div class="card shadow p-4">
            <h2 class="text-center sam mb-4">Cria a tua Conta</h2>
            <p class="text-center text-muted mb-4">Preenche o formulário abaixo para te registares na plataforma.</p>

            <?php
            if (isset($_SESSION['registration_errors'])) {
                foreach ($_SESSION['registration_errors'] as $error) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
                }
                unset($_SESSION['registration_errors']);
            }
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>

            <form action="./scripts/sc_registo.php" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" class="form-control" id="nome" placeholder="Insira o seu nome" name="nome" required>
                    <div class="valid-feedback">Tudo certo!</div>
                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" placeholder="Insira o seu email" name="email" required>
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
                    <button type="submit" class="btn btn-outline-verde">Registar</button>
                </div>
            </form>

        </div>
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

<style>
/* Optional: Custom styling for the toggle button */
#togglePassword {
    border-left: 0;
    border-color: #ced4da !important;
}

#togglePassword:focus {
    box-shadow: none !important;
    border-color: #ced4da !important;
}

#togglePassword:hover {
    border-color: #ced4da !important;
}

#togglePassword:active {
    border-color: #ced4da !important;
    box-shadow: none !important;
}

/* Ensure the input group looks seamless */
.input-group > .form-control:not(:last-child) {
    border-right: 0;
}

.input-group > .btn {
    border-left: 0;
    border-color: #ced4da !important;
}

.input-group > .form-control:focus {
    border-right: 0;
    box-shadow: none;
}

.input-group > .btn:focus {
    border-left: 0 !important;
    border-color: #ced4da !important;
    box-shadow: none !important;
}

/* Keep button border consistent when input is focused */
.input-group > .form-control:focus + .btn,
.input-group > .form-control:focus + .btn:hover,
.input-group > .form-control:focus + .btn:active,
.input-group > .form-control:focus + .btn:focus {
    border-color: #ced4da !important;
    box-shadow: none !important;
}

/* Override Bootstrap's input-group focus styles */
.input-group:focus-within .btn {
    border-color: #ced4da !important;
    box-shadow: none !important;
}
</style>