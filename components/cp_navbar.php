<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<section class="">
    <div class="container">
    </div>
    <div class="">
        <nav class="navbar  navbar-expand-lg border-bottom border-body w-100 pt-2 ps-3 pb-2">
            <div class="container-fluid">
                <a class="navbar-brand fs-2" style="font-family: 'Orbitron', sans-serif; font-weight: 700; background: linear-gradient(to right, #2EE0FF , #5EED5E); background-clip: text; -webkit-text-fill-color: transparent;" href="index.php">S.A.M</a>
                <button class="navbar-toggler text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span> Menu
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item fs-5 fw-semibold ps-3"><a href="index.php" class="nav-link textonav">Início</a></li>
                        <li class="nav-item fs-5 fw-semibold ps-3"><a href="ajuda.php" class="nav-link textonav">Ajuda</a></li>
                        <li class="nav-item fs-5 fw-semibold ps-3"><a href="#" class="nav-link textonav">Sobre nós</a></li>
                    </ul>
                    <ul class="navbar-nav ms-auto pe-2">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <!-- User is logged in - show profile and logout -->
                            <li class="nav-item dropdown fs-5 fw-semibold ps-3">
                                <a class="nav-link dropdown-toggle textonav" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if (!empty($_SESSION['user_imgperfil'])): ?>
                                        <img src="./uploads/<?php echo htmlspecialchars($_SESSION['user_imgperfil']); ?>" 
                                             alt="Profile" class="rounded-circle me-2" width="30" height="30">
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($_SESSION['user_nome']); ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                                    <?php if ($_SESSION['user_tipo_id'] == 4): // Administrator ?>
                                        <li><a class="dropdown-item" href="admin/dashboard.php">Administração</a></li>
                                    <?php elseif ($_SESSION['user_tipo_id'] == 3): // Moderator ?>
                                        <li><a class="dropdown-item" href="moderador/dashboard.php">Moderação</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="./scripts/sc_logout.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <!-- User is not logged in - show login and register -->
                            <li class="nav-item fs-5 fw-semibold ps-3">
                                <a href="login.php" class="nav-link textonav">Login</a>
                            </li>
                            <li class="nav-item fs-5 fw-semibold ps-3">
                                <a href="registo.php" class="nav-link textonav">Registo</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</section>