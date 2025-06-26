<?php
require_once './connections/connection.php';
$conn = new_db_connection();

try {
    $query = 'SELECT categorias.nome AS categoria_nome, servicos.nome AS servico_nome, servicos.capa AS servico_capa FROM categorias INNER JOIN servicos ON id_Categorias = ref_id_Categorias';
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Initialize arrays to store categories and services
    $categories = array();
    $unique_services = array();
    
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
    error_log("Database error: " . $e->getMessage());
    $categories = array();
    $unique_services = array();
}

// Handle session messages
$messages = '';
if (isset($_SESSION['logout_message'])) {
    $messages .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    $messages .= '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($_SESSION['logout_message']);
    $messages .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['logout_message']);
}

if (isset($_SESSION['success_message'])) {
    $messages .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    $messages .= '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($_SESSION['success_message']);
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
                                       class="form-control search-input" 
                                       placeholder="Procure por especialistas ou serviços..."
                                       list="services-list">
                                <button class="btn search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            
                            <!-- Services Datalist -->
                            <datalist id="services-list">
                                <?php foreach ($unique_services as $service): ?>
                                    <option value="<?php echo htmlspecialchars($service); ?>">
                                <?php endforeach; ?>
                            </datalist>
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
                if ($category_count >= 6) break; // Show only first 6 categories
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
/* Messages */
.messages-container {
    margin-bottom: 20px;
}

.alert {
    border-radius: 12px;
    border: none;
    padding: 15px 20px;
    margin-bottom: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
}

/* Hero Section */
.hero-wrapper {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 80px 0;
    margin-bottom: 60px;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1.3rem;
    color: #6c757d;
    margin-bottom: 40px;
    line-height: 1.6;
}

/* Search Section */
.search-section {
    margin-top: 40px;
}

.search-container {
    position: relative;
    max-width: 500px;
}

.search-input {
    padding: 15px 60px 15px 20px;
    border: 2px solid #e9ecef;
    border-radius: 50px;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.search-input:focus {
    border-color: #00798F;
    box-shadow: 0 4px 20px rgba(0,121,143,0.2);
}

.search-btn {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #00798F, #0493ad);
    color: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.search-btn:hover {
    background: linear-gradient(135deg, #0493ad, #00798F);
    transform: translateY(-50%) scale(1.05);
}

/* Hero Image */
.hero-image {
    text-align: center;
}

.image-credit {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 10px;
}

.image-credit a {
    color: #00798F;
    text-decoration: none;
}

/* Features Section */
.features-section {
    padding: 80px 0;
    background: white;
}

.feature-title {
    font-size: 2.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
}

.feature-text {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 15px;
    line-height: 1.6;
}

/* Quick Stats */
.quick-stats {
    display: flex;
    gap: 30px;
    margin-top: 30px;
}

.stat-item {
    text-align: center;
}

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
.categories-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 50px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.category-card {
    background: white;
    padding: 30px 20px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s;
    border: 2px solid transparent;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #00798F;
}

.category-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #00798F, #0493ad);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 1.5rem;
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

/* Call to Action */
.cta-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.cta-subtitle {
    font-size: 1.2rem;
    margin-bottom: 40px;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .search-input {
        font-size: 1rem;
        padding: 12px 50px 12px 15px;
    }
    
    .quick-stats {
        justify-content: center;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn {
        width: 250px;
    }
}

@media (max-width: 576px) {
    .hero-wrapper {
        padding: 40px 0;
    }
    
    .features-section,
    .categories-section,
    .cta-section {
        padding: 40px 0;
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .feature-title,
    .section-title,
    .cta-title {
        font-size: 1.8rem;
    }
}
</style>