<x-market-layout title="Fale Connosco — Cá do Bairro">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <!-- Page Header -->
                <div class="text-center mb-5">
                    <span class="badge-stamp mb-3 d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-envelope"></i>Selo · contacto
                    </span>
                    <h1 class="page-title mb-2" style="font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; letter-spacing: -0.02em; color: var(--ink); font-size: clamp(2rem, 4vw, 2.75rem);">
                        Fale <span style="color: var(--accent-1);">Connosco</span>
                    </h1>
                    <p class="mx-auto" style="max-width: 620px; font-size: 1.05rem; line-height: 1.6; color: #44594c;">
                        Tem alguma dúvida sobre o registo da sua loja, sugestão de melhoria ou gostaria de colaborar na valorização do comércio local? Envie-nos uma mensagem.
                    </p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center gap-3 p-3 mb-4 shadow-sm" style="background-color: #edf7f0; border: 2px solid var(--accent-1); color: var(--accent-2); border-radius: 14px;" role="alert">
                        <i class="fa-solid fa-circle-check fs-4 text-accent"></i>
                        <div>
                            <strong>Excelente!</strong> {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-start gap-3 p-3 mb-4 shadow-sm" style="background-color: #fdf2f2; border-radius: 14px; border: 2px solid #dc3545;" role="alert">
                        <i class="fa-solid fa-circle-exclamation fs-4 text-danger mt-1"></i>
                        <div>
                            <strong>Por favor, verifique os campos assinalados:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="row g-4 align-items-stretch">
                    <!-- Left: Contact Details & Info -->
                    <div class="col-lg-5">
                        <div class="h-100 p-4 p-md-5 d-flex flex-column justify-content-between position-relative overflow-hidden" style="background: #ffffff; border: 2px solid var(--ink); border-radius: 20px; box-shadow: 4px 4px 0 rgba(24, 43, 32, 0.12);">
                            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px);"></div>
                            
                            <div>
                                <h3 class="fw-bold mb-4" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ink); font-size: 1.45rem;">
                                    Canais Diretos
                                </h3>

                                <div class="d-flex flex-column gap-4">
                                    <!-- Email -->
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; border-radius: 12px; background: #edf5f0; color: var(--accent-1); border: 1.5px solid var(--ink); font-size: 1.2rem;">
                                            <i class="fa-solid fa-at"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: var(--ink); font-size: 0.95rem;">Email de Suporte</div>
                                            <a href="mailto:suporte@cadobairro.pt" class="text-decoration-none hover-underline small" style="color: #44594c;">suporte@cadobairro.pt</a>
                                        </div>
                                    </div>

                                    <!-- Schedule -->
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; border-radius: 12px; background: #edf5f0; color: var(--accent-1); border: 1.5px solid var(--ink); font-size: 1.2rem;">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: var(--ink); font-size: 0.95rem;">Atendimento</div>
                                            <div class="small" style="color: #44594c;">Segunda a Sexta: 09h00 – 18h00</div>
                                            <div class="text-muted small" style="font-size: 0.8rem;">(Exceto feriados nacionais)</div>
                                        </div>
                                    </div>

                                    <!-- Community / Proximity -->
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; border-radius: 12px; background: #edf5f0; color: var(--accent-1); border: 1.5px solid var(--ink); font-size: 1.2rem;">
                                            <i class="fa-solid fa-basket-shopping"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: var(--ink); font-size: 0.95rem;">Missão Local</div>
                                            <div class="small" style="color: #44594c;">Promover e digitalizar mercearias, padarias e feiras de bairro em Portugal.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick FAQ link stub -->
                            <div class="mt-4 pt-4 border-top" style="border-color: rgba(24, 43, 32, 0.12) !important;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="small">
                                        <div class="fw-bold text-ink">Procura respostas rápidas?</div>
                                        <div class="text-muted">Consulte as Perguntas Frequentes.</div>
                                    </div>
                                    <a href="{{ route('help') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.82rem; border-color: var(--ink);">
                                        Ver FAQ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Contact Form -->
                    <div class="col-lg-7">
                        <div class="h-100 p-4 p-md-5 position-relative overflow-hidden" style="background: #ffffff; border: 2px solid var(--ink); border-radius: 20px; box-shadow: 4px 4px 0 rgba(24, 43, 32, 0.12);">
                            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px);"></div>

                            <h3 class="fw-bold mb-4" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ink); font-size: 1.45rem;">
                                Enviar Mensagem
                            </h3>

                            <form action="{{ route('contact.submit') }}" method="POST">
                                @csrf

                                <!-- Name & Email row -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold small text-ink mb-1">O seu Nome <span class="text-danger">*</span></label>
                                        <input type="text" class="market-input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" required placeholder="Ex.: Maria Santos">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold small text-ink mb-1">Endereço de Email <span class="text-danger">*</span></label>
                                        <input type="email" class="market-input @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required placeholder="maria@exemplo.pt">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Subject -->
                                <div class="mb-3">
                                    <label for="subject" class="form-label fw-semibold small text-ink mb-1">Assunto <span class="text-danger">*</span></label>
                                    <select class="market-select @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                                        <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Selecione o motivo de contacto...</option>
                                        <option value="Dúvida Geral" {{ old('subject') === 'Dúvida Geral' ? 'selected' : '' }}>Dúvida Geral sobre a Plataforma</option>
                                        <option value="Registo de Loja" {{ old('subject') === 'Registo de Loja' ? 'selected' : '' }}>Registo ou Atualização de Loja</option>
                                        <option value="Código de Funcionário" {{ old('subject') === 'Código de Funcionário' ? 'selected' : '' }}>Código de Convite / Funcionários</option>
                                        <option value="Sugestão ou Parceria" {{ old('subject') === 'Sugestão ou Parceria' ? 'selected' : '' }}>Sugestão de Melhoria ou Parceria</option>
                                        <option value="Outro" {{ old('subject') === 'Outro' ? 'selected' : '' }}>Outro Assunto</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Message -->
                                <div class="mb-4">
                                    <label for="message" class="form-label fw-semibold small text-ink mb-1">Mensagem <span class="text-danger">*</span></label>
                                    <textarea class="market-input @error('message') is-invalid @enderror" id="message" name="message" rows="5" required placeholder="Como podemos ajudar?">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn-pill-mustard px-4 py-2 border-0 d-inline-flex align-items-center gap-2">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        <span>Enviar Mensagem</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-market-layout>
