<?php
require_once './connections/connection.php';
$conn = new_db_connection();

// Initialize arrays first
$categories = array();
$unique_services = array();
$stores = array();

// Fetch stores - separate try/catch
try {
    $query_lojas = 'SELECT id_Loja, nome_loja FROM lojas ORDER BY nome_loja';
    $stmt_lojas = $conn->prepare($query_lojas);
    $stmt_lojas->execute();
    
    while ($row = $stmt_lojas->fetch(PDO::FETCH_ASSOC)) {
        $stores[] = array(
            'id' => $row['id_Loja'],
            'nome' => $row['nome_loja']
        );
    }
} catch(PDOException $e) {
    error_log("Store fetch error: " . $e->getMessage());
    // Don't reset $stores here - keep any data we have
}

// Fetch categories and services - separate try/catch
try {
    $query = 'SELECT categorias.nome AS categoria_nome, servicos.nome AS servico_nome, servicos.capa AS servico_capa FROM categorias INNER JOIN servicos ON id_Categorias = ref_id_Categorias';
    $stmt = $conn->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categoria_nome = $row['categoria_nome'];
        $servico_nome = $row['servico_nome'];
        
        if (!isset($categories[$categoria_nome])) {
            $categories[$categoria_nome] = array();
        }
        
        $categories[$categoria_nome][] = $servico_nome;
        
        if (!in_array($servico_nome, $unique_services)) {
            $unique_services[] = $servico_nome;
        }
    }
} catch(PDOException $e) {
    error_log("Categories fetch error: " . $e->getMessage());
    // Don't reset arrays here
}

// Handle session messages
$messages = '';
if (isset($_SESSION['logout_message'])) {
    $messages .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    $messages .= '<i class="fas fa-check-circle me-2"></i>' . $_SESSION['logout_message'];
    $messages .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['logout_message']);
}

if (isset($_SESSION['success_message'])) {
    $messages .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    $messages .= '<i class="fas fa-check-circle me-2"></i>' . $_SESSION['success_message'];
    $messages .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['success_message']);
}
?>
<!-- NOTE: cp_head.php (included before this file) already provides the
     single <!DOCTYPE html><html><head>...</head><body> for the whole page.
     This file only contributes styles + markup for the hero/accordion
     section, not a second document. -->
<style>
:root{
  /* -- Mercado palette: awning canvas, market green, carimbo ink -- */
  --paper: #f5f2e6;        /* awning canvas / ledger paper */
  --paper-2: #ede6d2;      /* deeper canvas for cards */
  --card: #fffdf7;
  --ink: #182b20;          /* near-black green ink */
  --muted: #5c6b5f;
  --accent-1: #0f7a42;     /* market green */
  --accent-2: #0b5c33;     /* deep green */
  --ochre: #c17f1e;        /* carimbo / stamp ink */
  --ochre-deep: #93610f;
  --line: rgba(24,43,32,0.14);
  --radius: 14px;
}

/* Scoped page reset — was html,body{} + body{} before, which fought the
   flex/footer-pinning rules in meusestilos.css and let this cream
   "paper" color leak into the html/body background site-wide. */
.hero-page-wrapper{
  font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  background: var(--paper);
  color: var(--ink);
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
}

.label-stamp{
  font-family:'Space Mono', monospace;
  text-transform:uppercase;
  letter-spacing:.14em;
}

/* Messages container */
.messages-container .alert{border-radius:12px; font-family:'Inter',sans-serif;}

/* HERO */
.hero-grid {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  gap: 6vw;
  padding-top: 12px;
}
.hero-grid > * { flex: 1; }

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
  margin-bottom:18px;
  background: repeating-linear-gradient(45deg, transparent 0 4px, rgba(193,127,30,0.08) 4px 8px);
}
.badge-stamp i{font-size:.8rem}

.hero-title{
  font-family:'Bricolage Grotesque', 'Inter', sans-serif;
  font-size: clamp(2.6rem, 4.4vw, 3.6rem);
  font-weight:700;
  line-height:1.03;
  margin:0 0 14px;
  color: var(--ink);
  letter-spacing:-.01em;
}

.sam{
  font-weight:800;
  color: var(--accent-1);
  position:relative;
  white-space:nowrap;
}

.hero-subtitle{
  color:var(--muted);
  font-size:1.15rem;
  line-height:1.55;
  margin-bottom:30px;
  max-width:52ch;
}

/* Search — styled like a market-stall order counter */
.search-container{max-width:640px}

.search-card{
  display:flex;
  gap:10px;
  align-items:center;
  background: var(--card);
  border-radius:16px;
  padding:8px;
  box-shadow: 0 6px 20px rgba(24,43,32,0.08);
  border: 2px solid var(--ink);
  transition:transform .18s ease, box-shadow .18s ease;
}

.search-card:focus-within{
  transform:translateY(-3px);
  box-shadow: 0 12px 24px rgba(15,122,66,0.2);
  border-color: var(--accent-1);
}

.search-input{
  background:transparent;
  border:0;
  padding:16px 18px;
  font-size:1rem;
  color:inherit;
  outline: none;
  flex:1;
  font-family:'Inter', sans-serif;
}

.search-icon-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:52px;
  height:52px;
  border-radius:12px;
  border:0;
  cursor:pointer;
  font-size:1.05rem;
  background: var(--accent-1);
  color:#fff;
  box-shadow: 0 4px 0 var(--accent-2);
  transition: transform .12s ease;
}
.search-icon-btn:active{transform:translateY(3px); box-shadow:none;}

/* Suggestions */
.search-suggestions{
  position:absolute;
  left:0; right:0; top:100%;
  margin-top:12px;
  background:var(--card);
  border-radius:12px;
  overflow:hidden;
  border:2px solid var(--ink);
  box-shadow:0 10px 26px rgba(24,43,32,0.14);
  z-index:1200;
  max-height:320px;
  display:none;
}

.suggestion-item{
  display:flex;
  align-items:center;
  gap:12px;
  padding:13px 18px;
  cursor:pointer;
  transition:background .12s ease;
  border-bottom:1px dashed var(--line);
  color:var(--ink);
  font-size:.96rem;
}
.suggestion-item:last-child{border-bottom:0;}
.suggestion-item:hover,
.suggestion-item.active{
  background: rgba(15,122,66,0.08);
}
.no-results{color:var(--muted); cursor:default}
.match{background:rgba(193,127,30,0.18); padding:1px 5px; border-radius:5px}

/* Hero visual — framed like a raffle / market ticket */
.hero-visual{ display:flex; justify-content:center; }

.ticket-frame{
  position:relative;
  width:100%;
  max-width:420px;
}

.ticket-stamp{
  position:absolute;
  top:-14px; right:18px;
  width:74px; height:74px;
  border-radius:50%;
  border:2px solid var(--ochre);
  display:flex; align-items:center; justify-content:center;
  text-align:center;
  font-family:'Space Mono', monospace;
  font-size:.6rem;
  letter-spacing:.06em;
  color:var(--ochre-deep);
  background: var(--card);
  transform:rotate(10deg);
  text-transform:uppercase;
  line-height:1.15;
  box-shadow: 0 4px 10px rgba(24,43,32,0.1);
}

.hero-visual img{
  max-width: 100%;
  width: 100%;
  height: auto;
  display: block;
  border-radius:10px;
  transition:transform .5s ease;
}
.hero-visual img:hover{transform:translateY(-6px) rotate(-1deg)}
.image-credit{font-size:.76rem; color:var(--muted); margin-top:10px; text-align:center}

/* ---------- Scalloped awning dividers ---------- */
.scallop{
  height:22px;
  background-image: radial-gradient(circle at 11px 0, transparent 11px, var(--accent-1) 11.5px);
  background-size: 22px 22px;
  background-repeat: repeat-x;
  margin-left: calc(50% - 50vw);
  margin-right: calc(50% - 50vw);
}
.scallop-flip{ transform: rotate(180deg); }

/* ---------- Features band: market awning ---------- */
.features-accordion-section {
  margin-left: calc(50% - 50vw);
  margin-right: calc(50% - 50vw);
  padding: 56px 0 64px;
  background: repeating-linear-gradient(
    90deg,
    var(--accent-1) 0 40px,
    var(--accent-2) 40px 80px
  );
  background-color: var(--accent-1);
}

.features-inner {
  width: 100%;
  max-width: 1040px;
  margin: 0 auto;
  padding: 0 20px;
}

.features-inner .accordion { width: 100%; display:flex; flex-direction:column; gap:16px; }

/* Force Bootstrap's own radius vars to match our 12px cards on every
   corner/state (first/last item, collapsed/expanded) instead of fighting
   Bootstrap's more specific selectors with a plain .accordion-button rule */
#featuresAccordion{
  --bs-accordion-border-radius: 12px;
  --bs-accordion-inner-border-radius: 12px;
}

/* Ticket-stub style accordion item */
.features-inner .accordion-item {
  border: none;
  background: transparent;   /* was Bootstrap's default --bs-accordion-bg (square,
                                 opaque) showing through the button's rounded corners */
  border-radius: 12px;
}

.features-inner .accordion-button {
  width: 100%;
  display: flex;
  align-items: center;
  gap:16px;
  padding: 20px 22px;
  font-family:'Bricolage Grotesque', sans-serif;
  font-size: 1.2rem;
  font-weight: 700;
  border-radius: 12px;
  border: 2px solid var(--ink);
  background: var(--card);
  color: var(--ink);
  box-shadow: 4px 4px 0 rgba(11,92,51,0.35);
}

.features-inner .accordion-button .stub-num{
  font-family:'Space Mono', monospace;
  font-weight:700;
  font-size:.78rem;
  color:var(--ochre-deep);
  border:1px dashed var(--ochre);
  border-radius:8px;
  padding:4px 8px;
  flex-shrink:0;
}

.features-inner .accordion-button::after { opacity: 0.7; }

.features-inner .accordion-button:not(.collapsed) {
  background: var(--ink);
  color: #fff;
  border-color: var(--ink);
  box-shadow: 4px 4px 0 var(--ochre);
}
.features-inner .accordion-button:not(.collapsed) .stub-num{
  color:#f5d9a8;
  border-color:#f5d9a8;
}

.features-inner .accordion-body {
  background: var(--card);
  border: 2px dashed var(--line);
  padding: 18px 22px;
  color: #324037;
  border-radius: 12px;
  margin-top:10px;
  font-size: 1.02rem;
  line-height: 1.6;
}

@media (max-width: 780px) {
  .features-accordion-section { padding: 34px 0 44px; }
  .features-inner .accordion-button { font-size: 1.02rem; padding: 16px 14px; }
  .features-inner .accordion-body { font-size: .98rem; padding: 14px; }
}

/* Strong CTA — awning banner + carimbo button */
.cta-section{
  position:relative;
  padding:64px 32px 56px;
  border-radius:22px;
  text-align:center;
  background: var(--ink);
  color: var(--paper);
  overflow:hidden;
  margin-bottom: 5vh;
}
.cta-section::before{
  content:'';
  position:absolute; inset:0;
  background: repeating-linear-gradient(90deg, rgba(193,127,30,0.12) 0 36px, transparent 36px 72px);
  pointer-events:none;
}

.cta-section h3{
  font-family:'Bricolage Grotesque', sans-serif;
  font-size:2.1rem;
  font-weight:700;
  margin-bottom:8px;
  position:relative;
}

.cta-section h5{
  color: #cfe3d4;
  font-weight:500;
  position:relative;
}

.cta-buttons {
  display:flex;
  gap:14px;
  justify-content:center;
  flex-wrap:wrap;
  position:relative;
}

.btn-ghost{
  background: transparent;
  color:var(--paper);
  border:2px solid rgba(245,242,230,0.4);
  padding:11px 22px;
  border-radius:999px;
  font-weight:500;
  transition: border-color .15s ease, background .15s ease;
}
.btn-ghost:hover{
  background: rgba(245,242,230,0.08);
  border-color: var(--paper);
  color: var(--paper);
}

.btn-main-cta{
  background: var(--ochre);
  color:#241705 !important;
  padding:11px 26px;
  border-radius:999px;
  font-weight:700;
  border:2px solid #241705;
  box-shadow:4px 4px 0 rgba(0,0,0,0.35);
  transition: transform .12s ease, box-shadow .12s ease;
}
.btn-main-cta:hover{
  background:#d38f2c;
  transform: translate(-2px,-2px);
  box-shadow:6px 6px 0 rgba(0,0,0,0.35);
}

.hero-abstract{ width:100%; height:auto; display:block; }

/* Responsive */
@media(max-width:1000px){
  .hero-grid{ flex-direction:column; }
  .hero-visual{order:-1; margin-bottom:22px; width:100%;}
}
@media(max-width:540px){
  .search-input{padding:13px}
  .search-icon-btn{min-width:46px;height:46px}
  .scallop{ background-size: 18px 18px; background-image: radial-gradient(circle at 9px 0, transparent 9px, var(--accent-1) 9.5px); }
}
  </style>
  <div class="hero-page-wrapper">

  <div class="container mt-2">
    <div class="messages-container">
      <?php echo $messages; ?>
    </div>
  </div>

  <main class="container mt-4">
    <!-- HERO -->
<section class="hero-grid">
  <div>
    <span class="badge-stamp"><i class="fas fa-store"></i>Selo · comércio local</span>
    <h1 class="hero-title">Bem-vindo ao <span class="sam">S.A.M</span></h1>
    <p class="hero-subtitle">A base de dados de lojas que lhe permite apoiar o comércio local.</p>

    <!-- Search -->
    <div class="search-container position-relative">
      <div class="search-card" role="search" aria-label="Pesquisar lojas">
        <input id="store-search" class="search-input" type="search" placeholder="Procure por lojas" aria-autocomplete="list" aria-controls="search-suggestions" autocomplete="off" />
        <button id="search-btn" class="search-icon-btn" type="button" aria-label="Pesquisar">
          <i class="fas fa-search"></i>
        </button>
      </div>
      <div id="search-suggestions" class="search-suggestions" role="listbox" aria-label="Resultados da pesquisa"></div>
    </div>
  </div>

  <div class="hero-visual">
    <div class="ticket-frame">
      <div class="ticket-stamp">100%<br>local</div>
      <svg class="hero-abstract" viewBox="0 0 500 420" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustração abstrata: lupa e pin de localização sobre formas geométricas">
        <!-- soft background blobs -->
        <circle cx="150" cy="150" r="140" fill="var(--paper-2)" />
        <circle cx="360" cy="270" r="100" fill="var(--accent-1)" opacity="0.10" />
        <rect x="60" y="260" width="140" height="140" rx="28" fill="var(--ochre)" opacity="0.14" transform="rotate(8 130 330)" />

        <!-- scattered dashed rings (search / discovery motif) -->
        <circle cx="410" cy="90" r="34" fill="none" stroke="var(--ochre)" stroke-width="3" stroke-dasharray="6 7" />
        <circle cx="80" cy="330" r="22" fill="none" stroke="var(--accent-2)" stroke-width="3" stroke-dasharray="5 6" />

        <!-- location pin -->
        <g transform="translate(300,120)">
          <path d="M0 0 C 46 0 46 46 0 100 C -46 46 -46 0 0 0 Z" fill="var(--accent-1)" />
          <circle cx="0" cy="34" r="16" fill="var(--paper)" />
        </g>

        <!-- small storefront glyph -->
        <g transform="translate(140,190)">
          <rect x="-38" y="6" width="76" height="60" rx="6" fill="var(--card)" stroke="var(--ink)" stroke-width="3" />
          <path d="M-46 6 L-30 -26 L30 -26 L46 6 Z" fill="var(--ochre)" stroke="var(--ink)" stroke-width="3" />
          <rect x="-10" y="34" width="20" height="32" fill="var(--accent-2)" />
        </g>

        <!-- magnifying glass tying it to the search bar -->
        <g transform="translate(220,300)">
          <circle cx="0" cy="0" r="46" fill="var(--card)" stroke="var(--ink)" stroke-width="6" />
          <line x1="33" y1="33" x2="70" y2="70" stroke="var(--ink)" stroke-width="8" stroke-linecap="round" />
          <circle cx="0" cy="0" r="46" fill="none" stroke="var(--accent-1)" stroke-width="3" opacity="0.5" />
        </g>

        <!-- confetti dots -->
        <circle cx="430" cy="200" r="6" fill="var(--accent-2)" />
        <circle cx="60" cy="90" r="5" fill="var(--ochre)" />
        <circle cx="380" cy="360" r="7" fill="var(--accent-1)" />
      </svg>
    </div>
  </div>
</section>


    <!-- FEATURES -->
<div class="scallop mt-5"></div>
<section class="features-accordion-section">
  <div class="features-inner">
    <div class="accordion" id="featuresAccordion">

      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#featureOne" aria-expanded="true" aria-controls="featureOne">
            <span class="stub-num">Ficha 01</span> O que é?
          </button>
        </h2>
        <div id="featureOne" class="accordion-collapse collapse show" data-bs-parent="#featuresAccordion">
          <div class="accordion-body">
            Encontre uma loja local aonde pode fazer as suas compras — filtrável por localidade, categoria e avaliações (quando disponíveis). Informação clara para ajudar na sua decisão.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="headingTwo">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#featureTwo" aria-expanded="false" aria-controls="featureTwo">
            <span class="stub-num">Ficha 02</span> A quem se dirige?
          </button>
        </h2>
        <div id="featureTwo" class="accordion-collapse collapse" data-bs-parent="#featuresAccordion">
          <div class="accordion-body">
            Pessoas que procuram lojas locais de confiança em que podem apoiar o comércio local.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="headingThree">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#featureThree" aria-expanded="false" aria-controls="featureThree">
            <span class="stub-num">Ficha 03</span> Como funciona?
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
<div class="scallop scallop-flip mb-5"></div>

    <!-- CTA -->
    <section class="cta-section mt-5">
      <h3  >Não espere mais:</h3>
      <h5 class="mb-5">Começe já a poupar!</h5>
      <div class="cta-buttons mt-3">
        <a href="#" class="btn btn-ghost"> <i class="fas fa-info-circle me-2"></i>Aprenda mais</a>
        <a href="registo.php" class="btn-main-cta">
          <i class="fas fa-user-plus me-2"></i>Registe-se ou faça login!
        </a>
        <a href="loja.php" class="btn btn-ghost"> <i class="fas fa-search me-2"></i>Explore as lojas</a>
       
      </div>
    </section>

  </main>

  </div><!-- /.hero-page-wrapper -->

  <script>
    // Store data for search from PHP database
    const stores = <?php echo json_encode($stores); ?>;

    console.log('🔍 Search initialized with stores:', stores);
    console.log('📊 Total stores loaded:', stores ? stores.length : 0);

    // Get DOM elements
    const searchInput = document.getElementById('store-search');
    const suggestionsDiv = document.getElementById('search-suggestions');

    console.log('📝 Input element:', searchInput);
    console.log('📋 Suggestions div:', suggestionsDiv);

    if (!searchInput) {
        console.error('❌ Search input not found!');
    }
    if (!suggestionsDiv) {
        console.error('❌ Suggestions div not found!');
    }
    if (!stores || stores.length === 0) {
        console.warn('⚠️ No stores data available!');
    }

    // Handle input changes
    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        console.log('⌨️ Input event fired. Query:', query);

        // Clear suggestions if input is empty
        if (query.length === 0) {
            console.log('🧹 Clearing suggestions (empty query)');
            suggestionsDiv.innerHTML = '';
            suggestionsDiv.style.display = 'none';
            return;
        }

        // Filter stores based on query - only match names that START with the query
        const matches = stores.filter(store => {
            const storeName = store.nome.toLowerCase();
            const isMatch = storeName.startsWith(query);
            console.log(`  Checking "${store.nome}": ${isMatch}`);
            return isMatch;
        });

        console.log('✅ Matches found:', matches.length, matches);

        // Display suggestions
        if (matches.length > 0) {
            const html = matches.map(store => 
                `<div class="suggestion-item" data-id="${store.id}">
                    <i class="fas fa-store me-2"></i>${store.nome}
                </div>`
            ).join('');
            
            console.log('📄 Generated HTML:', html);
            suggestionsDiv.innerHTML = html;
            suggestionsDiv.style.display = 'block';
            console.log('👁️ Suggestions div display:', suggestionsDiv.style.display);
            console.log('📐 Suggestions div dimensions:', {
                width: suggestionsDiv.offsetWidth,
                height: suggestionsDiv.offsetHeight,
                top: suggestionsDiv.offsetTop
            });
            
            // Add click handlers to suggestions
            const items = document.querySelectorAll('.suggestion-item');
            console.log('🖱️ Adding click handlers to', items.length, 'items');
            items.forEach(item => {
                item.addEventListener('click', function() {
                    const storeId = this.getAttribute('data-id');
                    console.log('🎯 Clicked store ID:', storeId);
                    window.location.href = `loja.php?id=${storeId}`;
                });
            });
        } else {
            console.log('❌ No matches - showing "no results" message');
            suggestionsDiv.innerHTML = '<div class="suggestion-item no-results">Nenhuma loja encontrada</div>';
            suggestionsDiv.style.display = 'block';
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            console.log('🚪 Clicked outside - hiding suggestions');
            suggestionsDiv.style.display = 'none';
        }
    });

    // Show suggestions when input is focused and has value
    searchInput.addEventListener('focus', function() {
        console.log('🎯 Input focused. Current value:', this.value);
        if (this.value.trim().length > 0) {
            console.log('🔄 Re-triggering input event');
            this.dispatchEvent(new Event('input'));
        }
    });

    console.log('✅ Search script fully loaded and ready');
  </script>