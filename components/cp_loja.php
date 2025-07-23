<?php
// Include the connection file
$connection_file = './connections/connection.php';

if (!file_exists($connection_file)) {
    die("<div class='alert alert-danger'>ERROR: Database connection file not found.</div>");
}

require_once $connection_file;

// Get the PDO connection from your function
try {
    if (function_exists('new_db_connection')) {
        $pdo = new_db_connection();
    } else {
        die("<div class='alert alert-danger'>ERROR: Database connection function not found.</div>");
    }
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Database connection error: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Test the connection
try {
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Database connection test failed: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Get store ID from URL parameter
$store_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($store_id <= 0) {
    header('Location: lojas.php');
    exit();
}

try {
    // Get store information with employee count
    $stmt = $pdo->prepare("
        SELECT l.*, 
               COUNT(fl.ref_id_Funcionario) as total_funcionarios,
               DATE_FORMAT(l.inicio, '%d/%m/%Y') as data_inicio
        FROM lojas l
        LEFT JOIN funcionarios_lojas fl ON l.id_Loja = fl.ref_id_Loja
        WHERE l.id_Loja = ?
        GROUP BY l.id_Loja
    ");
    $stmt->execute([$store_id]);
    $loja = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$loja) {
        header('Location: lojas.php');
        exit();
    }

    // Get store services with categories
    $stmt = $pdo->prepare("
        SELECT s.nome, c.Nome as categoria_nome
        FROM servicos s
        INNER JOIN categorias c ON s.ref_id_Categorias = c.id_Categorias
        ORDER BY c.Nome, s.nome
    ");
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get unique categories for the store
    $categorias = array_unique(array_column($servicos, 'categoria_nome'));

    // Function to get service icon based on service name and category
    function getServiceIcon($serviceName, $categoria) {
        $serviceName = strtolower($serviceName);
        $categoria = strtolower($categoria);
        
        if (strpos($serviceName, 'pasta') !== false && strpos($serviceName, 'dentes') !== false) {
            return 'fas fa-tooth';
        } elseif (strpos($serviceName, 'escova') !== false && strpos($serviceName, 'dentes') !== false) {
            return 'fas fa-spa';
        } elseif (strpos($serviceName, 'alface') !== false) {
            return 'fas fa-leaf';
        } elseif (strpos($serviceName, 'patinhos') !== false) {
            return 'fas fa-baby';
        } elseif ($categoria === 'higiene') {
            return 'fas fa-soap';
        } elseif ($categoria === 'alimentos') {
            return 'fas fa-utensils';
        } else {
            return 'fas fa-concierge-bell';
        }
    }

    // Function to get store initials
    function getStoreInitials($name) {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        return substr($initials, 0, 2);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: lojas.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Loja - SAM</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .store-header {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .store-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .service-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .service-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .service-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: linear-gradient(135deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .map-container {
            height: 300px;
            background: #e9ecef;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .rating-stars {
            color: #ffc107;
        }

        .page-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="">
        <!-- Navigation -->

        <!-- Main Content -->
        <div class="main-content">
            <div class="container">
                <!-- Page Header -->
                <div class="page-header rounded mt-4">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="mb-0">Detalhes da Loja</h1>
                                <p class="mb-0">Informações completas sobre a loja</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="lojas.php" class="btn btn-outline-light">
                                    <i class="fas fa-arrow-left me-2"></i>Voltar às Lojas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                        <!-- Left Column - Store Info -->
                        <div class="col-lg-8">
                            <!-- Store Header -->
                            <div class="store-header shadow">
                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center">
                                        <div class="store-avatar">
                                            <span><?php echo getStoreInitials($loja['nome_loja']); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex align-items-center mb-2">
                                            <h2 class="mb-0 me-3"><?php echo htmlspecialchars($loja['nome_loja']); ?></h2>
                                            <span class="store-status status-active">
                                                <i class="fas fa-circle me-1"></i>Ativa
                                            </span>
                                        </div>
                                        <div class="rating-stars mb-2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <span class="ms-2 text-muted">4.5 (23 avaliações)</span>
                                        </div>
                                        <p class="text-muted mb-3"><?php echo htmlspecialchars($loja['descricao'] ?: 'Sem descrição disponível'); ?></p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="info-label">Membro desde</div>
                                                <div class="info-value"><?php echo $loja['data_inicio']; ?></div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-label">Total de Funcionários</div>
                                                <div class="info-value"><?php echo $loja['total_funcionarios']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Store Location -->
                            <div class="card store-card">
                                 <div class="card-header mb-3">
                                    <h5 class=" mb-0">
                                        <i class="fas fa-info-circle me-2 "></i>Localização
                                    </h5>
                                </div>
                                <div class="row mb-3 ps-4">
                                    <div class="col-md-6">
                                        <div class="info-label">Latitude</div>
                                        <div class="info-value"><?php echo $loja['lat']; ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Longitude</div>
                                        <div class="info-value"><?php echo $loja['lon']; ?></div>
                                    </div>
                                </div>
                                <div class="map-container">
                                    <div class="text-center">
                                        <i class="fas fa-map fa-3x mb-3"></i>
                                        <p>Mapa da localização da loja</p>
                                        <small class="text-muted">Place ID: <span><?php echo htmlspecialchars($loja['place_id']); ?></span></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Available Services -->
                            <div class="card store-card mt-5">
                                
                                <div class="card-header mb-3">
                                    <h5 class=" mb-0">
                                        <i class="fas fa-info-circle me-2 "></i>Informações da Loja
                                    </h5>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3 ps-4 pe-4">
                                    <h5 class="mb-0">
                                        <i class="fas fa-concierge-bell text-success me-2"></i>
                                        Serviços Disponíveis
                                    </h5>
                                    <span class="badge bg-success"><?php echo count($servicos); ?> serviços</span>
                                </div>
                                <div class="row ps-4 pe-4 pb-4">
                                    <?php if (!empty($servicos)): ?>
                                        <?php foreach ($servicos as $servico): ?>
                                            <div class="col-md-6">
                                                <div class="service-card shadow">
                                                    <div class="d-flex align-items-center">
                                                        <div class="service-image me-3">
                                                            <i class="<?php echo getServiceIcon($servico['nome'], $servico['categoria_nome']); ?>"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($servico['nome']); ?></h6>
                                                            <small class="text-muted">Categoria: <?php echo htmlspecialchars($servico['categoria_nome']); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Esta loja ainda não tem serviços cadastrados.
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Reviews Section -->
                            <div class="card store-card mt-5 mb-5">
                                  <div class="card-header mb-3">
                                    <h5 class=" mb-0">
                                        <i class="fas fa-info-circle me-2 "></i>Avaliações dos clientes
                                    </h5>
                                </div>
                           
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Ainda não existem avaliações para esta loja. Seja o primeiro a avaliar!
                                </div>
                            </div>
                        </div>

                        <!-- Right Sidebar -->
                        <div class="col-lg-4">
                            <!-- Quick Stats -->
                            <div class="stats-card mb-4 ps-4 pt-4 pb-4">
                                <h5 class="mb-3">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Estatísticas
                                </h5>
                                <div class="row justify-content-end mb-3">
                                    <div class="col-6">
                                        <h3 class="mb-0"><?php echo count($servicos); ?></h3>
                                        <small>Serviços</small>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="mb-0"><?php echo $loja['total_funcionarios']; ?></h3>
                                        <small>Funcionários</small>
                                    </div>
                                </div>
                                <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                                <div class="row">
                                    <div class="col-6">
                                        <h3 class="mb-0">4.5</h3>
                                        <small>Classificação</small>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="mb-0">23</h3>
                                        <small>Avaliações</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Actions -->
                            <div class="card shadow pt-4 ps-4 pe-4 pb-4">
                                <h5 class="sam mb-3">Ações Rápidas</h5>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-verde" onclick="avaliarLoja()">
                                        <i class="fas fa-star me-2"></i>Avaliar Loja
                                    </button>
                                    <button class="btn btn-outline-azul" onclick="verNoMapa(<?php echo $loja['lat']; ?>, <?php echo $loja['lon']; ?>)">
                                        <i class="fas fa-map-marker-alt me-2"></i>Ver no Mapa
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="partilharLoja()">
                                        <i class="fas fa-share-alt me-2"></i>Partilhar
                                    </button>
                                </div>
                            </div>

                            <!-- Store Categories -->
                            <div class="card store-card">
                                  <div class="card-header mb-3">
                                    <h5 class=" mb-0">
                                        <i class="fas fa-info-circle me-2 "></i>Categorias
                                    </h5>
                                </div>
                                <div class="d-flex flex-wrap gap-2 pe-4 ps-4 pb-4">
                                    <?php if (!empty($categorias)): ?>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($categoria); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sem categorias definidas</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script>
        function avaliarLoja() {
            alert('Funcionalidade de avaliação em desenvolvimento!');
        }

        function verNoMapa(lat, lon) {
            window.open(`https://www.google.com/maps?q=${lat},${lon}`, '_blank');
        }

        function partilharLoja() {
            if (navigator.share) {
                navigator.share({
                    title: `<?php echo isset($loja['nome_loja']) ? htmlspecialchars($loja['nome_loja']) : 'Loja'; ?> - SAM`,
                    text: `Confira esta loja no SAM: <?php echo isset($loja['descricao']) ? htmlspecialchars($loja['descricao'] ?: $loja['nome_loja']) : 'Detalhes da loja'; ?>`,
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copiado para a área de transferência!');
                }).catch(() => {
                    // If clipboard API also fails, show the URL
                    prompt('Copie este link:', window.location.href);
                });
            }
        }
    </script>
</body>
</html>