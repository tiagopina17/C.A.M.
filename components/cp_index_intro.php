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
        $servico_capa = $row['servico_capa'];
        
        if (!isset($categories[$categoria_nome])) {
            $categories[$categoria_nome] = array();
        }
        $categories[$categoria_nome][] = array('nome' => $servico_nome, 'capa' => $servico_capa);

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

if (isset($_SESSION['error_message'])) {
    $messages .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    $messages .= '<i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($_SESSION['error_message']);
    $messages .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['error_message']);
}

$conn = null;
?>

<!-- Messages Container -->
<?php if ($messages): ?>
<div class="messages-container">
    <div class="container">
        <?php echo $messages; ?>
    </div>
</div>
<?php endif; ?>

<!-- Hero Section -->
<div class="hero-wrapper">
    <div class="container">
        <div class="hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-text">
                        <h1 class="hero-title">
                            Bem-vindo ao <span class="sam">S.A.M</span>
                        </h1>
                        <p class="hero-subtitle">
                            A base de dados de especialistas em Portugal, para problemas que o ChatGPT não resolve.
                        </p>
                        
                        <!-- Search Bar -->
                        <div class="search-section">
                            <div class="search-container">
                                <input type="text" 
                                       id="store-search"
                                       class="form-control search-input" 
                                       placeholder="Procure por lojas, especialistas ou serviços..."
                                       autocomplete="off">
                                <button class="btn search-btn" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                                
                                <!-- Custom dropdown for search suggestions -->
                                <div id="search-suggestions" class="search-suggestions"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="images/confused2.png" class="img-fluid" alt="Illustration of confused person">
                        <p class="image-credit">
                            <a href="https://www.freepik.com/free-vector/confused-person-sitting-with-laptop-question-marks-puzzled-young-man-thinking-about-answer-flat-vector-illustration-faq-research-stress-concept-banner-website-design-landing-web-page_26876776.htm" target="_blank">
                                Image by pch.vector on Freepik
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="features-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <div class="feature-content">
                    <h2 class="feature-title">A solução para si</h2>
                    <p class="feature-text">
                        Encontre o especialista ideal para o seu problema.
                    </p>
                    <p class="feature-text">
                        Começe já a pesquisar entre milhares de especialistas!
                    </p>
                    
                    <!-- Quick Stats -->
                    <div class="quick-stats">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($unique_services); ?>+</div>
                            <div class="stat-label">Serviços</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($categories); ?>+</div>
                            <div class="stat-label">Categorias</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($stores); ?>+</div>
                            <div class="stat-label">Lojas</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 order-lg-1">
                <div class="feature-image">
                    <img src="images/working.png" class="img-fluid" alt="Working professionals">
                    <p class="image-credit">
                        <a href="https://www.freepik.com/free-vector/household-renovation-professions-set_9924535.htm" target="_blank">
                            Image by freepik
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Preview -->
<?php if (!empty($categories)): ?>
<div class="categories-section">
    <div class="container">
        <h2 class="section-title text-center">Explore as nossas categorias</h2>
        <div class="categories-grid">
            <?php 
            $category_count = 0;
            foreach ($categories as $category_name => $services): 
                if ($category_count >= 6) break;
            ?>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h4 class="category-name"><?php echo htmlspecialchars($category_name); ?></h4>
                    <p class="category-count"><?php echo count($services); ?> serviços</p>
                </div>
            <?php 
                $category_count++;
            endforeach; 
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Call to Action -->
<div class="cta-section">
    <div class="container">
        <div class="cta-content text-center">
            <h2 class="cta-title">Não espere mais</h2>
            <p class="cta-subtitle">Comece a sua jornada connosco hoje</p>
            
            <div class="cta-buttons">
                <a href="#" class="btn btn-outline-azul btn-lg me-3">
                    <i class="fas fa-info-circle me-2"></i>Aprenda mais
                </a>
                <a href="#" class="btn btn-outline-azul btn-lg me-3">
                    <i class="fas fa-search me-2"></i>Explore os serviços
                </a>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <a href="perfil.php" class="btn btn-outline-verde btn-lg">
                        <i class="fas fa-user me-2"></i>Meu Perfil
                    </a>
                <?php else: ?>
                    <a href="registo.php" class="btn btn-outline-verde btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Registe-se
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>



/* Quick Stats */

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #00798F, #0E810E);
    background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 5px;
}

/* Categories Section */

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 40px;
}




.category-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.category-count {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
}



</style>

<script>
// Store data for search
const stores = <?php echo json_encode($stores); ?>;

// Get DOM elements
const searchInput = document.getElementById('store-search');
const suggestionsDiv = document.getElementById('search-suggestions');

// Handle input changes
searchInput.addEventListener('input', function() {
    const query = this.value.trim().toLowerCase();
    
    // Clear suggestions if input is empty
    if (query.length === 0) {
        suggestionsDiv.innerHTML = '';
        suggestionsDiv.style.display = 'none';
        return;
    }
    
    // Filter stores based on query - only match names that START with the query
    const matches = stores.filter(store => {
        return store.nome.toLowerCase().startsWith(query);
    });
    
    // Display suggestions
    if (matches.length > 0) {
        suggestionsDiv.innerHTML = matches.map(store => 
            `<div class="suggestion-item" data-id="${store.id}">
                <i class="fas fa-store me-2"></i>${store.nome}
            </div>`
        ).join('');
        suggestionsDiv.style.display = 'block';
        
        // Add click handlers to suggestions
        document.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const storeId = this.getAttribute('data-id');
                window.location.href = `loja.php?id=${storeId}`;
            });
        });
    } else {
        suggestionsDiv.innerHTML = '<div class="suggestion-item no-results">Nenhuma loja encontrada</div>';
        suggestionsDiv.style.display = 'block';
    }
});

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
        suggestionsDiv.style.display = 'none';
    }
});

// Show suggestions when input is focused and has value
searchInput.addEventListener('focus', function() {
    if (this.value.trim().length > 0) {
        this.dispatchEvent(new Event('input'));
    }
});
</script>