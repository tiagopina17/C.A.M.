<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>S.A.M - Sistema de Apoio ao Mercado</title>

  <!-- External --- Bootstrap + icons + fonts (kept) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
 :root{
  --bg: #f9fafb; /* light background */
  --card: #ffffff;
  --muted: #5f6b6b;
  --accent-1: #16a34a; /* main green */
  --accent-2: #0d9241; /* darker green */
  --glass: rgba(0,0,0,0.03);
  --glass-2: rgba(0,0,0,0.02);
  --radius: 14px;
  --glass-border: rgba(0,0,0,0.06);
}

/* Page reset */
html,body{height:100%;}
body{
  margin:0;
  font-family: Inter, Poppins, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  background: var(--bg);
  color: #1a1a1a;
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
}

.container{max-width:1100px}

/* Messages container */
.messages-container .alert{border-radius:12px;}

/* HERO */
.hero-section{
  position:relative;
}

.hero-grid{
  display:grid;
  grid-template-columns: 1fr 480px;
  gap:48px;
  align-items:center;
}

.hero-title{
  font-size: clamp(2.2rem, 4.2vw, 3.4rem);
  font-weight:700;
  line-height:1.02;
  margin:0 0 12px;
  background: linear-gradient(90deg, var(--accent-1), var(--accent-2));
  -webkit-background-clip:text;
  background-clip:text;
  color:transparent;
}

.sam{font-family: 'Orbitron', Inter, sans-serif;}

.hero-subtitle{
  color:var(--muted);
  font-size:1.05rem;
  margin-bottom:28px;
  max-width:60ch;
}

/* Search */
.search-container{max-width:760px}

.search-card{
  display:flex;
  gap:10px;
  align-items:center;
  background: #fff;
  border-radius:999px;
  padding:8px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.06);
  border: 1px solid #e5e7eb;
  transition:transform .18s ease, box-shadow .18s ease;
}

.search-card:focus-within{
  transform:translateY(-4px);
  box-shadow: 0 12px 24px rgba(22,163,74,0.15);
}

.search-input{
  background:transparent;
  border:0;
  padding:18px 18px;
  font-size:1rem;
  color:inherit;
  outline: none;
  flex:1;
}

.search-icon-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:54px;
  height:54px;
  border-radius:999px;
  border:0;
  cursor:pointer;
  font-size:1.05rem;
  background:linear-gradient(135deg,var(--accent-1),var(--accent-2));
  color:#fff;
  box-shadow: 0 6px 12px rgba(22,163,74,0.3);
}

/* Suggestions */
.search-suggestions{
  position:absolute;
  left:0; right:0; top:100%;
  margin-top:14px;
  background:#fff;
  border-radius:12px;
  overflow:hidden;
  border:1px solid rgba(0,0,0,0.08);
  box-shadow:0 8px 24px rgba(0,0,0,0.1);
  z-index:1200;
  max-height:320px;
  display:none;
}

.suggestion-item{
  display:flex;
  align-items:center;
  gap:12px;
  padding:14px 18px;
  cursor:pointer;
  transition:background .12s ease;
  border-bottom:1px solid rgba(0,0,0,0.04);
  color:#1a1a1a;
}
.suggestion-item:hover,
.suggestion-item.active{
  background:rgba(22,163,74,0.08);
}
.no-results{color:var(--muted); cursor:default}
.match{background:rgba(22,163,74,0.1); padding:2px 6px; border-radius:6px}

/* Hero image */
.hero-visual img{
  width:100%;
  max-width:420px;
  transition:transform .9s ease;
}
.hero-visual img:hover{transform:translateY(-8px) rotate(-1deg)}
.image-credit{font-size:.78rem; color:var(--muted); margin-top:10px}

/* Features */
/* ---------- Full-bleed features band (UPDATED) ---------- */

/* full-bleed across viewport even if inside a centered container */
.features-accordion-section {
  margin-left: calc(50% - 50vw);
  margin-right: calc(50% - 50vw);
  padding: 64px 0;
  /* new calmer background (soft, neutral mint/gray) */
background: linear-gradient(
  to bottom,
  rgba(16, 204, 97, 0) 0%,      /* transparent only at the top edge */
  rgba(16, 204, 97, 0.25) 10%,  /* fade in quickly */
  rgba(16, 204, 97, 0.25) 90%,  /* stay fully visible the whole way */
  rgba(16, 204, 97, 0) 100%     /* fade out only at the bottom edge */
);

  /* subtle separation line at top */
  box-shadow: inset 0 1px 0 rgba(0,0,0,0.03);
}

/* Inner area: remove max-width limit so items can expand,
   but keep side gutters for readability */
.features-inner {
  width: 100%;
  max-width: none;         /* <-- ensures it's not constrained to 1100px */
  margin: 0 auto;
  padding: 0 20px;         /* safe horizontal gutters */
}

/* Make accordion children span the available width */
.features-inner .accordion {
  width: 100%;
}

/* Each item should stretch across the inner gutters */
.features-inner .accordion-item {
  border: none;
  margin-bottom: 18px;
}

/* Big full-width button (left text, right chevron) */
.features-inner .accordion-button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 22px 20px;
  font-size: 1.25rem;
  font-weight: 600;
  border-radius: 10px;
  border: 1px solid rgba(0,0,0,0.06);
  background: #ffffff;
  color: #0b3c2d;
  box-shadow: 0 6px 16px rgba(0,0,0,0.04);
}

/* Larger chevron (Bootstrap default ::after kept) */
.features-inner .accordion-button::after {
  font-size: 1.05rem;
  opacity: 0.8;
}

/* Open state — keep brand green but subtle */
.features-inner .accordion-button:not(.collapsed) {
  background: linear-gradient(135deg, rgba(22,163,74,0.95), rgba(13,146,65,0.95));
  color: #fff;
  border-color: rgba(0,0,0,0.06);
  box-shadow: 0 10px 28px rgba(22,163,74,0.18);
}

/* Body content visually attached to the button and full width */
.features-inner .accordion-body {
  background: #ffffff;
  border: 1px solid rgba(0,0,0,0.06);
  border-top: none;
  padding: 20px;
  color: #324047;
  border-radius: 0 0 10px 10px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.03);
  font-size: 1.03rem;
  line-height: 1.6;
}

/* Responsive tweaks */
@media (max-width: 780px) {
  .features-accordion-section { padding: 40px 0; }
  .features-inner { padding: 0 14px; }
  .features-inner .accordion-button { font-size: 1.05rem; padding: 16px 14px; }
  .features-inner .accordion-body { font-size: 1rem; padding: 14px; }
}



.quick-stats{display:flex; gap:16px; margin-top:22px}
.stat-item{
  flex:1;
  padding:18px;
  border-radius:12px;
  background:#f3f4f6;
  text-align:center;
}
.stat-number{font-size:1.4rem; font-weight:700; color:var(--accent-1)}
.stat-label{color:var(--muted)}





@media(max-width:1000px){
  .features-section {
    grid-template-columns: 1fr;
  }
}

/* Strong CTA */
.cta-section{
  padding:60px 32px;
  border-radius:18px;
  text-align:center;
  background-color: rgba(16, 204, 75, 0.65);
  color: white;
  box-shadow: 0 12px 32px rgba(0,0,0,0.12);
  margin-bottom: 5vh;
}

/* Headline */
.cta-section h3{
  font-size:2rem;
  font-weight:700;
  margin-bottom:10px;
}

/* Subtext */
.cta-section p{
  font-size:1.1rem;
  opacity: .95;
  margin-bottom: 26px;
}

/* Buttons */
.cta-buttons {
  display:flex;
  gap:14px;
  justify-content:center;
  flex-wrap:wrap;
}

/* Ghost buttons on green */
.btn-ghost{
  background: rgba(255,255,255,0.15);
  color:white;
  border:1px solid rgba(255,255,255,0.5);
  padding:12px 22px;
  border-radius:999px;
  font-weight:500;
}
.btn-ghost:hover{
  background: rgba(255,255,255,0.28);
}

/* Main CTA */
.btn-main-cta{
  background:white;
  color:var(--accent-2) !important;
  padding:12px 28px;
  border-radius:999px;
  font-weight:600;
  box-shadow:0 8px 20px rgba(0,0,0,0.2);
}
.btn-main-cta:hover{
  background:#f5f5f5;
}


/* Responsive */
@media(max-width:1000px){
  .hero-grid{grid-template-columns:1fr;}
  .hero-visual{order:-1;margin-bottom:18px}
  .features-section{grid-template-columns:1fr}
}
@media(max-width:540px){
  .search-input{padding:14px}
  .search-icon-btn{min-width:48px;height:48px}



/* Force accordion to span page nicely */
.custom-accordion {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Accordion core styles */
.custom-accordion .accordion-item {
  border: none;
  margin-bottom: 16px;
}

.custom-accordion .accordion-button {
  background: white;
  font-size: 1.3rem;
  font-weight: 600;
  padding: 22px;
  border-radius: 12px;
  color: #0b3c2d;
  border: 1px solid rgba(0,0,0,0.07);
}

.custom-accordion .accordion-button:not(.collapsed) {
  background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
  color: #fff;
  box-shadow: 0 8px 20px rgba(22,163,74,0.25);
}

.custom-accordion .accordion-body {
  background: white;
  border: 1px solid rgba(0,0,0,0.07);
  border-top: none;
  padding: 22px;
  font-size: 1.05rem;
  color: #374151;
  border-radius: 0 0 12px 12px;
}

}

  </style>
</head>
<body>
  <div class="container mt-2">
    <div class="messages-container"></div>
  </div>

  <main class="container mt-4">
    <!-- HERO -->
    <section class="hero-section">
      <div class="hero-grid">
        <div>
          <h1 class="hero-title">Bem-vindo ao <span class="sam">S.A.M</span></h1>
          <p class="hero-subtitle">A base de dados de lojas que lhe permite apoiar o comércio local.</p>

          <!-- Search -->
          <div class="search-container position-relative">
            <div class="search-card" role="search" aria-label="Pesquisar lojas">
              <input id="store-search" class="search-input" type="search" placeholder="Procure por lojas" aria-autocomplete="list" aria-controls="search-suggestions" autocomplete="off" />
              <button id="search-btn" class="search-icon-btn" aria-label="Pesquisar">
                <i class="fas fa-search"></i>
              </button>
            </div>

            <div id="search-suggestions" class="search-suggestions" role="listbox" aria-label="Resultados da pesquisa"></div>
          </div>
        </div>

        <div class="hero-visual">
          <img src="images/confused2.png" alt="Ilustração de pessoa confusa com portátil" />
        </div>
      </div>

      <!-- small credits -->
      <p class="image-credit text-end">Image by pch.vector on Freepik</p>
    </section>

    <!-- FEATURES -->
<section class="features-accordion-section mt-5 mb-5">
  <div class="features-inner">
    <div class="accordion" id="featuresAccordion">

      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#featureOne" aria-expanded="true" aria-controls="featureOne">
            O que é?
          </button>
        </h2>
        <div id="featureOne" class="accordion-collapse collapse show" data-bs-parent="#featuresAccordion">
          <div class="accordion-body">
            Encontre o especialista ideal para o seu problema — filtrável por localidade, categoria e avaliações (quando disponíveis). Informação clara para ajudar na sua decisão.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="headingTwo">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#featureTwo" aria-expanded="false" aria-controls="featureTwo">
            A quem se dirige?
          </button>
        </h2>
        <div id="featureTwo" class="accordion-collapse collapse" data-bs-parent="#featuresAccordion">
          <div class="accordion-body">
            Pessoas que procuram serviços locais de confiança — consumidores, empresas pequenas ou quem precisa de uma reparação rápida perto de si.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="headingThree">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#featureThree" aria-expanded="false" aria-controls="featureThree">
            Como funciona?
          </button>
        </h2>
        <div id="featureThree" class="accordion-collapse collapse" data-bs-parent="#featuresAccordion">
          <div class="accordion-body">
            Pesquise, veja perfis de lojas, compare avaliações e contacte directamente. Filtros por cidade, categoria e tipo de serviço tornam a busca mais rápida.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>



 



    <!-- CTA -->
    <section class="cta-section mt-5">
      <h3 style="margin-bottom:6px">Não espere mais</h3>
      <p class="text-muted">Comece a sua jornada connosco hoje</p>
      <div class="cta-buttons mt-3">
        <a href="#" class="btn btn-ghost"> <i class="fas fa-info-circle me-2"></i>Aprenda mais</a>
        <a href="#" class="btn btn-ghost"> <i class="fas fa-search me-2"></i>Explore os serviços</a>
       <a href="#" class="btn-main-cta">
        <i class="fas fa-user-plus me-2"></i>Registe-se
       </a>
      </div>
    </section>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ===== Example data (replace with server data) =====
    const stores = [
      {id:1, nome:'Loja Exemplo 1'},
      {id:2, nome:'Loja Exemplo 2'},
      {id:3, nome:'Loja Teste'},
      {id:4, nome:'Especialista Silva'},
      {id:5, nome:'Reparações João'}
    ];

    // ===== Helpers =====
    const $ = (s) => document.querySelector(s);
    const $$ = (s) => Array.from(document.querySelectorAll(s));

    const searchInput = $('#store-search');
    const suggestions = $('#search-suggestions');
    const searchBtn = $('#search-btn');

    // Debounce helper
    function debounce(fn, delay=220){
      let t; return (...args)=>{ clearTimeout(t); t = setTimeout(()=>fn(...args), delay); };
    }

    // Create suggestion list
    function renderSuggestions(list, query){
      if(!list.length){
        suggestions.innerHTML = `<div class="suggestion-item no-results">Nenhuma loja encontrada</div>`;
        suggestions.style.display = 'block';
        return;
      }

      const html = list.map((s, idx) => {
        // highlight match
        const regex = new RegExp('(' + escapeRegex(query) + ')', 'ig');
        const name = s.nome.replace(regex, '<span class="match">$1</span>');
        return `<div class="suggestion-item" role="option" data-id="${s.id}" data-idx="${idx}">
                  <i class="fas fa-store me-2"></i>
                  <div style="flex:1">${name}</div>
                  <div class="text-muted" style="font-size:.85rem">Ver</div>
                </div>`;
      }).join('');

      suggestions.innerHTML = html;
      suggestions.style.display = 'block';

      // attach click handlers
      $$('.suggestion-item').forEach(item => {
        item.addEventListener('click', () => {
          const id = item.dataset.id; window.location.href = `loja.php?id=${id}`;
        });
      });
    }

    function escapeRegex(s){ return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

    function searchStores(q){
      if(!q) return [];
      q = q.trim().toLowerCase();
      // simple startsWith + includes fallback
      const starts = stores.filter(s => s.nome.toLowerCase().startsWith(q));
      const includes = stores.filter(s => !starts.includes(s) && s.nome.toLowerCase().includes(q));
      return [...starts, ...includes];
    }

    // Debounced input handler
    const handleInput = debounce(function(e){
      const q = e.target.value;
      if(!q || q.trim().length === 0){ suggestions.style.display = 'none'; return; }
      const results = searchStores(q);
      renderSuggestions(results, q);
    }, 160);

    searchInput.addEventListener('input', handleInput);

    // keyboard navigation
    let activeIndex = -1;
    searchInput.addEventListener('keydown', function(e){
      const items = $$('.suggestion-item');
      if(suggestions.style.display !== 'block') return;

      if(e.key === 'ArrowDown'){
        e.preventDefault(); activeIndex = Math.min(activeIndex + 1, items.length -1);
        updateActive(items);
      } else if(e.key === 'ArrowUp'){
        e.preventDefault(); activeIndex = Math.max(activeIndex -1, 0);
        updateActive(items);
      } else if(e.key === 'Enter'){
        e.preventDefault(); if(activeIndex >=0 && items[activeIndex]) items[activeIndex].click(); else searchBtn.click();
      } else if(e.key === 'Escape'){
        suggestions.style.display = 'none';
      }
    });

    function updateActive(items){
      items.forEach(it => it.classList.remove('active'));
      if(activeIndex >= 0 && items[activeIndex]){
        items[activeIndex].classList.add('active');
        items[activeIndex].scrollIntoView({block:'nearest'});
      }
    }

    // Close when clicking outside
    document.addEventListener('click', (ev)=>{
      if(!ev.composedPath().includes(searchInput) && !ev.composedPath().includes(suggestions) && !ev.composedPath().includes(searchBtn)){
        suggestions.style.display = 'none';
      }
    });

    // Simple UX: clicking search button will navigate to first match or to a search results page
    searchBtn.addEventListener('click', ()=>{
      const q = searchInput.value.trim();
      if(!q) return;
      const res = searchStores(q);
      if(res.length) window.location.href = `loja.php?id=${res[0].id}`;
      else {
        // fallback: go to search results page with query param
        window.location.href = `search.php?q=${encodeURIComponent(q)}`;
      }
    });

    // Accessibility: announce live (very small polyfill)
    const liveRegion = document.createElement('div');
    liveRegion.setAttribute('aria-live','polite');
    liveRegion.style.position='absolute'; liveRegion.style.left='-9999px';
    document.body.appendChild(liveRegion);

    // When suggestions open, announce count
    const obs = new MutationObserver(()=>{
      const items = $$('.suggestion-item').filter(i => !i.classList.contains('no-results'));
      if(suggestions.style.display === 'block') liveRegion.textContent = items.length ? `${items.length} resultados` : 'Nenhum resultado encontrado';
    });
    obs.observe(suggestions, {childList:true, subtree:true});

  </script>
</body>
</html>
