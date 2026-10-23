<div class="container main-content d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 120px);">
    <div class="col-md-6 col-lg-9">
        <div class="card shadow p-4">
            <h2 class="text-center sam mb-4">Recuperar palavra passe</h2>
            <p class="text-center text-muted mb-4">Recupera a tua palavra passe inserindo o teu email abaixo.</p>

            <?php
            // Display login errors if they exist
            if (isset($_SESSION['login_errors']) && !empty($_SESSION['login_errors'])) {
                echo '<div class="alert alert-danger">';
                foreach ($_SESSION['login_errors'] as $error) {
                    echo '<div>' . htmlspecialchars($error) . '</div>';
                }
                echo '</div>';
                unset($_SESSION['login_errors']);
            }
            
            // Also keep support for single error (if used elsewhere)
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            ?>

            <form action="./scripts/sc_reset.php" method="post" class="needs-validation" novalidate>
                <div class="mb-5">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" placeholder="Insira o seu email" name="email" 
                           value="<?php echo isset($_SESSION['login_email']) ? htmlspecialchars($_SESSION['login_email']) : ''; ?>" required>
                    <div class="valid-feedback">Tudo certo!</div>
                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                </div>

                

                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-verde">Recuperar</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="./login_funcionario.php" class="fw-semibold fs-6 ver-mais-link">É funcionário ou proprietário de uma loja? Clique aqui para entrar</a>
            </div>
        </div>
    </div>
</div>

<?php
// Clear the login_email from session after displaying
if (isset($_SESSION['login_email'])) {
    unset($_SESSION['login_email']);
}
?>



