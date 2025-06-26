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
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                                        <li><hr class="dropdown-divider"></li>

                                        <?php if ($_SESSION['user_tipo_id'] == 4): // Administrator ?>
                                            <li><a class="dropdown-item" href="administracao">Administração</a></li>
                                        <?php elseif ($_SESSION['user_tipo_id'] == 3): // Moderator ?>
                                            <li>    <a class="dropdown-item" href="moderador/dashboard.php">Moderação</a></li>
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


    <script>
    //Para o Tiago do futuro: O JS do Bootstrap não carrega em apenas algumas páginas (?) então fazemos isto manualmente.
    //Se no futuro houver problemas podemos (ou melhor, podes) aplicar isto apenas nessas páginas específicas.
    //Como e aonde aplicar? Quem sabe...
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.querySelector('#navbarDropdown');
        const menu = document.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            toggle.removeAttribute('data-bs-toggle');
            toggle.removeAttribute('data-bs-target');
            
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (menu.style.display === 'block') {
                    menu.style.display = 'none';
                } else {
                    const rect = toggle.getBoundingClientRect();
                    const viewport = window.innerWidth;
                    
                    // Base positioning
                    menu.style.position = 'fixed';
                    menu.style.top = (rect.bottom + 5) + 'px';
                    menu.style.display = 'block';
                    menu.style.zIndex = '9999';
                    menu.style.backgroundColor = 'white';
                    menu.style.border = '1px solid #ccc';
                    menu.style.padding = '8px 0';
                    menu.style.borderRadius = '4px';
                    menu.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                    
                    // Responsive positioning
                    if (viewport < 768) {
                        // Mobile: wider dropdown, positioned from left edge of toggle
                        menu.style.left = rect.left + 'px';
                        menu.style.right = 'auto';
                        menu.style.minWidth = '200px';
                        menu.style.maxWidth = (viewport - rect.left - 20) + 'px';
                    } else {
                        // Desktop: right-aligned
                        menu.style.right = '10px';
                        menu.style.left = 'auto';
                        menu.style.minWidth = '160px';
                        menu.style.maxWidth = 'none';
                    }
                }
            });
            
            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    menu.style.display = 'none';
                }
            });
            
            // Reposition on window resize
            window.addEventListener('resize', function() {
                if (menu.style.display === 'block') {
                    menu.style.display = 'none';
                }
            });
        }
    });
    </script>