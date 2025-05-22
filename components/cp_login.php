<form class="col-6 mt-5 mb-5 ms-5 ps-5" action="./scripts/sc_login.php" method="post" class="was-validated">
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