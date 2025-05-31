
<div class="container mt-5 mb-5">
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
                    <input type="password" class="form-control" id="password" placeholder="Insira a sua password" name="password" required>
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
