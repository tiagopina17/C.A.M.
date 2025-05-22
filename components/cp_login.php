
<div class="container mt-5 mb-5">
    <div class="col-md-8 mx-auto">
        <div class="card shadow p-4">
            <h2 class="text-center sam mb-4">Iniciar Sessão</h2>
            <p class="text-center text-muted mb-4">Acede à tua conta inserindo os teus dados abaixo.</p>

            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            ?>

            <form action="./scripts/sc_login.php" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" placeholder="Insira o seu email" name="email" required>
                    <div class="valid-feedback">Tudo certo!</div>
                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="password" placeholder="Insira a sua password" name="password" required>
                    <div class="valid-feedback">Tudo certo!</div>
                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-azul">Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
