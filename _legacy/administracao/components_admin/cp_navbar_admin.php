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
                        <li class="nav-item fs-5 fw-semibold ps-3"><a href="../index.php" class="nav-link textonav">Início</a></li>
                        <li class="nav-item fs-5 fw-semibold ps-3"><a href="testes" class="nav-link textonav">Testes</a></li>
                    </ul>
                    <ul class="navbar-nav ms-auto pe-2">
                            <!-- User is logged in - show profile and logout -->
                            <li class="nav-item dropdown fs-5 fw-semibold ps-3">
                               
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                                        <li><a class="dropdown-item" href="admin/dashboard.php">Administração</a></li>
                                        <li><a class="dropdown-item" href="moderador/dashboard.php">Moderação</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="./scripts/sc_logout.php">Logout</a></li>
                                </ul>
                            </li>
                    
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</section>