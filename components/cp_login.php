<style>
:root{
  /* -- Mercado palette: awning canvas, market green, carimbo ink -- */
  --paper: #f5f2e6;
  --paper-2: #ede6d2;
  --card: #fffdf7;
  --ink: #182b20;
  --muted: #5c6b5f;
  --accent-1: #0f7a42;
  --accent-2: #0b5c33;
  --ochre: #c17f1e;
  --ochre-deep: #93610f;
  --line: rgba(24,43,32,0.14);
  --radius: 14px;
}

.auth-page-wrapper{
  font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  background: var(--paper);
  color: var(--ink);
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
  min-height: calc(100vh - 120px);
  display:flex;
  align-items:center;
  justify-content:center;
  padding: 48px 16px;
}

.badge-stamp{
  display:inline-flex;
  align-items:center;
  gap:8px;
  border:2px solid var(--ochre);
  border-radius:999px;
  padding:6px 16px;
  font-size:.72rem;
  color:var(--ochre-deep);
  transform:rotate(-3deg);
  margin-bottom:16px;
  background: repeating-linear-gradient(45deg, transparent 0 4px, rgba(193,127,30,0.08) 4px 8px);
}
.badge-stamp i{font-size:.8rem}

.sam{
  font-weight:800;
  color: var(--accent-1);
  white-space:nowrap;
}

.auth-card{
  background: var(--card);
  border: 2px solid var(--ink);
  border-radius: var(--radius);
  box-shadow: 6px 6px 0 var(--accent-2);
  padding: 2.6rem 2.4rem;
  width: 100%;
  max-width: 480px;
}

.auth-title{
  font-family:'Bricolage Grotesque', 'Inter', sans-serif;
  font-size: clamp(1.7rem, 3vw, 2.1rem);
  font-weight:700;
  line-height:1.1;
  margin:0 0 10px;
  color: var(--ink);
  letter-spacing:-.01em;
  text-align:center;
}

.auth-subtitle{
  color:var(--muted);
  font-size:.98rem;
  line-height:1.55;
  margin-bottom:26px;
  text-align:center;
}

.form-label-stamp{
  font-family:'Space Mono', monospace;
  text-transform:uppercase;
  letter-spacing:.1em;
  font-size:.74rem;
  color: var(--muted);
  display:block;
  margin-bottom:7px;
}

.form-control-mkt{
  width:100%;
  border:2px solid var(--ink);
  border-radius:10px;
  padding:12px 14px;
  font-family:'Inter', sans-serif;
  font-size:.98rem;
  background: var(--paper);
  color: var(--ink);
  transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
}
.form-control-mkt:focus{
  outline:none;
  border-color: var(--accent-1);
  background: var(--card);
  box-shadow: 0 0 0 3px rgba(15,122,66,0.15);
}

.input-group-mkt{
  display:flex;
  align-items:stretch;
  border:2px solid var(--ink);
  border-radius:10px;
  background: var(--paper);
  overflow:hidden;
  transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
}
.input-group-mkt:focus-within{
  border-color: var(--accent-1);
  background: var(--card);
  box-shadow: 0 0 0 3px rgba(15,122,66,0.15);
}
.input-group-mkt .form-control-mkt-flat{
  flex:1;
  border:0;
  background:transparent;
  padding:12px 14px;
  font-family:'Inter', sans-serif;
  font-size:.98rem;
  color: var(--ink);
}
.input-group-mkt .form-control-mkt-flat:focus{outline:none;}
.toggle-pw-btn{
  border:0;
  border-left:2px solid var(--ink);
  background: transparent;
  padding:0 16px;
  color: var(--muted);
  cursor:pointer;
  transition: color .15s ease, background .15s ease;
}
.toggle-pw-btn:hover{ color: var(--accent-1); background: rgba(15,122,66,0.06); }

.auth-forgot{
  font-size:.86rem;
}
.auth-forgot a{ color: var(--muted); text-decoration:underline; }
.auth-forgot a:hover{ color: var(--accent-1); }

.btn-mkt-submit{
  width:100%;
  background: var(--accent-1);
  color:#fff;
  border:2px solid var(--accent-2);
  border-radius:999px;
  padding:13px;
  font-family:'Bricolage Grotesque', sans-serif;
  font-weight:700;
  font-size:1rem;
  box-shadow: 4px 4px 0 var(--accent-2);
  transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
}
.btn-mkt-submit:hover{
  background:#0d6b3a;
  color:#fff;
  transform: translate(-2px,-2px);
  box-shadow: 6px 6px 0 var(--accent-2);
}
.btn-mkt-submit:active{ transform:translate(0,0); box-shadow:2px 2px 0 var(--accent-2); }

.auth-alert{
  border-radius:12px;
  border:2px solid;
  font-family:'Inter', sans-serif;
  font-size:.92rem;
  padding:.85rem 1rem;
}
.auth-alert.alert-danger{ background:#fdf1ec; border-color:#b3411f; color:#7a2c14; }

.auth-link-line{
  text-align:center;
  margin-top:22px;
  font-size:.92rem;
}
.auth-link{ color: var(--accent-1); font-weight:600; text-decoration:none; }
.auth-link:hover{ color: var(--accent-2); text-decoration:underline; }
</style>

<div class="auth-page-wrapper">
    <div class="auth-card">

        <h2 class="auth-title">Iniciar <span class="sam">Sessão</span></h2>
        <p class="auth-subtitle">Acede à tua conta inserindo os teus dados abaixo.</p>

        <?php
        // Display login errors if they exist
        if (isset($_SESSION['login_errors']) && !empty($_SESSION['login_errors'])) {
            echo '<div class="auth-alert alert-danger mb-3">';
            foreach ($_SESSION['login_errors'] as $error) {
                echo '<div>' . htmlspecialchars($error) . '</div>';
            }
            echo '</div>';
            unset($_SESSION['login_errors']);
        }

        // Also keep support for single error (if used elsewhere)
        if (isset($_SESSION['login_error'])) {
            echo '<div class="auth-alert alert-danger mb-3">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
            unset($_SESSION['login_error']);
        }
        ?>

        <form action="./scripts/sc_login.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label-stamp">Email</label>
                <input type="email" class="form-control form-control-mkt" id="email" placeholder="Insira o seu email" name="email"
                       value="<?php echo isset($_SESSION['login_email']) ? htmlspecialchars($_SESSION['login_email']) : ''; ?>" required>
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
                <div class="mt-2 auth-forgot">
                    <a href="#">Esqueci-me da minha palavra passe</a>
                </div>
                <div class="valid-feedback">Tudo certo!</div>
                <div class="invalid-feedback">Este campo é obrigatório.</div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn-mkt-submit">Entrar</button>
            </div>
        </form>

        <div class="auth-link-line">
            <a href="./login_funcionario.php" class="auth-link">É funcionário ou proprietário de uma loja? Clique aqui para entrar</a>
        </div>
    </div>
</div>

<?php
// Clear the login_email from session after displaying
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