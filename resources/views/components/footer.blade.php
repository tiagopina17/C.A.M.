<footer class="footer text-white pt-5 pb-4 mt-auto" style="background-color: var(--ink, #182b20) !important; border-top: 3px solid var(--ochre); color: var(--paper) !important;">
    <div class="container">
        <div class="row g-4 justify-content-between mb-4">
            <!-- Brand & Bio -->
            <div class="col-lg-5 col-md-6">
                <a class="fs-2 sam d-inline-block mb-2 text-decoration-none" href="{{ url('/') }}">S.A.M</a>
                <p class="text-white-50 small mb-3" style="max-width: 380px; line-height: 1.6;">
                    Sistema de Apoio ao Mercado local. Descubra lojas de proximidade, consulte produtos e serviços tradicionais e dinamize o comércio da sua comunidade.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="social-icon-pill" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon-pill" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon-pill" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="col-lg-3 col-md-3 col-6">
                <h6 class="text-uppercase fw-bold text-mustard mb-3" style="font-family: 'Space Mono', monospace; font-size: 0.85rem; letter-spacing: 0.1em;">Navegação</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li><a href="{{ url('/') }}" class="text-white-50 text-decoration-none footer-link">Início</a></li>
                    <li><a href="{{ url('/explorar') }}" class="text-white-50 text-decoration-none footer-link">Explorar Serviços</a></li>
                    <li><a href="{{ url('/mapa') }}" class="text-white-50 text-decoration-none footer-link">Mapa Interativo</a></li>
                    <li><a href="{{ url('/ajuda') }}" class="text-white-50 text-decoration-none footer-link">Perguntas Frequentes</a></li>
                </ul>
            </div>

            <!-- Merchants & Community -->
            <div class="col-lg-3 col-md-3 col-6">
                <h6 class="text-uppercase fw-bold text-mustard mb-3" style="font-family: 'Space Mono', monospace; font-size: 0.85rem; letter-spacing: 0.1em;">Comerciantes</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li><a href="{{ route('register') }}" class="text-white-50 text-decoration-none footer-link">Criar Conta</a></li>
                    <li><a href="{{ route('login') }}" class="text-white-50 text-decoration-none footer-link">Área de Funcionário</a></li>
                    <li><a href="{{ url('/ajuda') }}" class="text-white-50 text-decoration-none footer-link">Como Registar a Loja</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="border-top pt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small text-white-50" style="border-color: rgba(245,242,230,0.12) !important;">
            <div>
                © {{ date('Y') }} S.A.M — Desenvolvido por 
                <a class="text-mustard text-decoration-underline" href="https://www.youtube.com/watch?v=dQw4w9WgXcQ&pp=ygUXbmV2ZXIgZ29ubmEgZ2l2ZSB5b3UgdXA%3D" target="_blank" rel="noopener">Tiago Pina</a>
            </div>
            <div>
                Comércio tradicional e de proximidade
            </div>
        </div>
    </div>
</footer>
