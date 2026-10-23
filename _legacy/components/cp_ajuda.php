<style>
.ajuda-header{
  text-align:center;
  margin-bottom: 2rem;
}
.ajuda-header .badge-stamp{ margin-bottom: 14px; }
.ajuda-title{
  font-family:'Bricolage Grotesque', 'Inter', sans-serif;
  font-size: clamp(1.7rem, 3vw, 2.1rem);
  font-weight:700;
  color: var(--ink);
  letter-spacing:-.01em;
  margin: 0 0 8px;
}
.ajuda-subtitle{
  color: var(--muted);
  font-size: .98rem;
  max-width: 560px;
  margin: 0 auto;
}
</style>

<div class="dashboard-page-wrapper">
    <div class="container-fluid mt-5 mb-5 main-content">
        <div class="row justify-content-center">
            <div class="col-10 mx-auto">
                <div class="ajuda-header">
                    <span class="badge-stamp"><i class="fas fa-circle-question"></i>Selo · ajuda</span>
                    <h2 class="ajuda-title">Perguntas <span class="sam">Frequentes</span></h2>
                    <p class="ajuda-subtitle">Encontra respostas rápidas às dúvidas mais comuns sobre a plataforma.</p>
                </div>

                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Como crio uma conta na plataforma?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                Basta clicar em <strong>"Criar Conta"</strong> no topo da página e preencher o nome, email e password. Depois de submeteres o formulário, a tua conta fica ativa de imediato e já podes iniciar sessão.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Como registo a minha loja?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                Acede a <strong>"Regista a tua Loja"</strong>, indica o nome e descrição do negócio e, no segundo passo, procura e confirma a localização exata da loja no mapa. Assim que confirmares, a loja fica associada à tua conta.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Esqueci-me da password. E agora?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                Na página de login encontras o link <strong>"Esqueci-me da minha palavra passe"</strong>, mesmo por baixo do campo de password. Segue as instruções para definires uma nova password em segurança.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Como funciona o código de funcionário?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                O proprietário da loja gera um código único a partir da página de gestão da loja. O funcionário introduz esse código na sua área para se associar automaticamente à loja. Cada código expira 7 dias depois de ser gerado.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Não encontrei resposta à minha dúvida. Como contacto o suporte?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                Podes entrar em <a href="./contacto.php">contacto</a> connosco diretamente e a nossa equipa responde o mais brevemente possível.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>