<?php
session_start();
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
<form class="col-6 mt-5 mb-5 ms-5 ps-5" action="./scripts/sc_registo.php" method="post" class="was-validated">
    <div class="mb-3 mt-3">
        <label for="nome" class="form-label">Nome:</label>
        <input type="text" class="form-control" id="nome"
               placeholder="Insira o seu nome" name="nome" required>
        <div class="valid-feedback">Valid.</div>
        <div class="invalid-feedback">Este campo é obrigatório.</div>
    </div>

    <div class="mb-3 mt-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" id="email"
               placeholder="Insira o seu email" name="email" required>
        <div class="valid-feedback">Valid.</div>
        <div class="invalid-feedback">Este campo é obrigatório.</div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" class="form-control" id="password"
               placeholder="Insira a sua password" name="password" required>
        <div class="valid-feedback">Valid.</div>
        <div class="invalid-feedback">Este campo é obrigatório.</div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>