<x-market-layout title="Perguntas Frequentes — Cá do Bairro">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Page Header -->
                <div class="text-center mb-5">
                    <span class="badge-stamp mb-3 d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-question"></i>Selo · ajuda
                    </span>
                    <h1 class="page-title mb-2" style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; letter-spacing: -0.02em; color: var(--ink); font-size: clamp(2rem, 4vw, 2.75rem);">
                        Perguntas <span style="color: var(--accent-1);">Frequentes</span>
                    </h1>
                    <p class="mx-auto" style="max-width: 600px; font-size: 1.05rem; line-height: 1.6; color: #44594c;">
                        Encontre respostas claras sobre como descobrir o comércio tradicional à sua porta ou como registar e dinamizar o seu negócio no bairro.
                    </p>
                </div>

                <!-- FAQ Accordion with crisp white cards and clear distinction -->
                <div class="accordion help-faq-accordion mb-5" id="helpAccordion">
                    <!-- Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <span class="faq-num">01</span>
                                <span class="faq-title">Como descubro lojas e mercados no meu bairro?</span>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                Pode pesquisar diretamente pelo nome do estabelecimento no campo de pesquisa da página inicial ou navegar em <a href="{{ route('explore') }}">Lojas do Bairro</a> com filtros práticos de categoria (como Padarias, Talhos, Mercearias e Frutarias). Se preferir explorar visualmente por proximidade geográfica, consulte o nosso <a href="{{ route('map') }}">Mapa Interativo</a>.
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <span class="faq-num">02</span>
                                <span class="faq-title">Como crio uma conta na plataforma?</span>
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                Basta clicar no botão <strong>"Registo"</strong> no topo da página e preencher o seu nome, endereço de email e palavra-passe. A sua conta fica ativa de imediato, permitindo consultar perfis, partilhar lojas com a vizinhança ou iniciar o registo do seu negócio.
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <span class="faq-num">03</span>
                                <span class="faq-title">Como posso registar a minha loja ou mercado?</span>
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                Para comerciantes, o processo é simples e rápido:
                                <ol class="mt-2 mb-2 ps-3">
                                    <li>Inicie sessão com a sua conta de utilizador.</li>
                                    <li>Aceda à opção <strong>"Registar Loja"</strong>.</li>
                                    <li>Preencha a identificação básica (nome do estabelecimento e descrição).</li>
                                    <li>Pesquise a localização no mapa assistido pela Google e confirme as coordenadas exatas do seu espaço comercial.</li>
                                </ol>
                                A sua loja fica registada e associada à sua conta como <strong>proprietário</strong>, pronta a receber visitas da comunidade.
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                <span class="faq-num">04</span>
                                <span class="faq-title">Como funciona o código de convite para colaboradores?</span>
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                Cada proprietário de loja pode gerar na sua área de gestão um <strong>código de convite alfanumérico seguro</strong> (com validade de 7 dias). Os funcionários ou colaboradores da loja introduzem esse código na respetiva área de perfil para ficarem automaticamente associados à equipa da loja, com permissões para atualizar informação sem burocracias.
                            </div>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <span class="faq-num">05</span>
                                <span class="faq-title">Esqueci-me da minha palavra-passe. O que devo fazer?</span>
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                No ecrã de <a href="{{ route('login') }}">Login</a>, clique na hiperligação <strong>"Esqueceu-se da sua palavra-passe?"</strong>. Introduza o endereço de email associado à sua conta para receber um link seguro de reposição e definir uma nova senha.
                            </div>
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                <span class="faq-num">06</span>
                                <span class="faq-title">O registo da loja tem custos associados?</span>
                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                Não. O projeto <strong>Cá do Bairro</strong> é uma iniciativa dedicada ao fortalecimento do comércio tradicional e de proximidade. O registo de comerciantes e a consulta pública por clientes são totalmente gratuitos.
                            </div>
                        </div>
                    </div>

                    <!-- Item 7 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSeven">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                <span class="faq-num">07</span>
                                <span class="faq-title">Não encontrei resposta à minha dúvida. Como posso contactar o suporte?</span>
                            </button>
                        </h2>
                        <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                Pode enviar-nos uma mensagem diretamente através da nossa página de <a href="{{ route('contact') }}">Contacto</a>. A nossa equipa responderá com a maior brevidade possível.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dark Grounding CTA Panel (Deep Ink #182b20, high contrast, non-bright) -->
                <div class="help-dark-cta p-4 p-md-5 text-center shadow">
                    <div class="cta-icon-badge mb-3">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h2 class="mb-2">Ainda ficou com alguma dúvida?</h2>
                    <p class="mx-auto mb-4" style="max-width: 520px;">
                        A nossa equipa está disponível para ajudar no registo da sua loja, esclarecer dúvidas e apoiar a sua presença digital de proximidade.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('contact') }}" class="btn-pill-mustard text-decoration-none px-4 py-2 d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Fale Connosco</span>
                        </a>
                        <a href="{{ route('explore') }}" class="btn-ghost d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-store"></i>
                            <span>Ver Lojas</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-market-layout>
