<?php
require_once '../connections/connection.php';
//TODO: IMPORTAR ESTILOS
// Check if user is admin/has backoffice access
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_tipo_id'] !== 4) {    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
    
    exit;
}

$conn = new_db_connection();

try {
    // Get dashboard statistics
    $stats = array();
    
    // Total users
    $query = "SELECT COUNT(*) as total FROM users";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Total services
    $query = "SELECT COUNT(*) as total FROM servicos";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['total_services'] = $stmt->fetchColumn();
    
    // Total categories
    $query = "SELECT COUNT(*) as total FROM categorias";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['total_categories'] = $stmt->fetchColumn();
    
    // Recent registrations (last 30 days)
    $query = "SELECT COUNT(*) as total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['recent_users'] = $stmt->fetchColumn();
    
    // Get recent users
    $query = "SELECT nome, email, created_at, tipo_conta FROM users ORDER BY created_at DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories with service counts
    $query = "SELECT c.nome as categoria_nome, COUNT(s.id) as total_servicos 
              FROM categorias c 
              LEFT JOIN servicos s ON c.id = s.ref_id_Categorias 
              GROUP BY c.id, c.nome 
              ORDER BY total_servicos DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $categories_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get system activity (mock data - replace with actual activity log)
    $activities = array(
        array('action' => 'Novo utilizador registado', 'user' => 'João Silva', 'time' => '2 min atrás'),
        array('action' => 'Serviço adicionado', 'user' => 'Maria Santos', 'time' => '15 min atrás'),
        array('action' => 'Categoria criada', 'user' => 'Admin', 'time' => '1 hora atrás'),
        array('action' => 'Perfil actualizado', 'user' => 'Pedro Costa', 'time' => '2 horas atrás'),
        array('action' => 'Novo utilizador registado', 'user' => 'Ana Ferreira', 'time' => '3 horas atrás')
    );
    
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $stats = array('total_users' => 0, 'total_services' => 0, 'total_categories' => 0, 'recent_users' => 0);
    $recent_users = array();
    $categories_stats = array();
}

// Handle session messages
$messages = '';
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
    <div class="container-fluid">
        <?php echo $messages; ?>
    </div>
</div>
<?php endif; ?>



<!-- Dashboard Content -->
<div class="dashboard-content mt-4">
    <div class="container-fluid">
        
        <!-- Statistics Cards -->
        <div class="stats-section">
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card stat-users">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
                            <div class="stat-label">Total de Utilizadores</div>
                            <div class="stat-change">
                                <i class="fas fa-arrow-up"></i>
                                +<?php echo $stats['recent_users']; ?> este mês
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card stat-services">
                        <div class="stat-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-number"><?php echo number_format($stats['total_services']); ?></div>
                            <div class="stat-label">Serviços Registados</div>
                            <div class="stat-change">
                                <i class="fas fa-chart-line"></i>
                                Activos
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card stat-categories">
                        <div class="stat-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-number"><?php echo number_format($stats['total_categories']); ?></div>
                            <div class="stat-label">Categorias</div>
                            <div class="stat-change">
                                <i class="fas fa-tags"></i>
                                Organizadas
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stat-card stat-activity">
                        <div class="stat-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Taxa de Actividade</div>
                            <div class="stat-change">
                                <i class="fas fa-arrow-up"></i>
                                +2% vs mês anterior
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Dashboard Grid -->
        <div class="row">
            
            <!-- Recent Users -->
            <div class="col-lg-8 mb-4">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-users me-2"></i>Utilizadores Recentes
                        </h5>
                        <a href="users_management.php" class="btn btn-outline-azul btn-sm">
                            Ver Todos
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Data de Registo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?php echo strtoupper(substr($user['nome'], 0, 1)); ?>
                                                </div>
                                                <span><?php echo htmlspecialchars($user['nome']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $user['tipo_conta'] === 'admin' ? 'danger' : ($user['tipo_conta'] === 'especialista' ? 'success' : 'primary'); ?>">
                                                <?php echo ucfirst($user['tipo_conta']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions & Activity -->
            <div class="col-lg-4">
                
                <!-- Quick Actions -->
                <div class="dashboard-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-lightning-bolt me-2"></i>Ações Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="add_user.php" class="quick-action-btn">
                                <i class="fas fa-user-plus"></i>
                                <span>Adicionar Utilizador</span>
                            </a>
                            <a href="add_service.php" class="quick-action-btn">
                                <i class="fas fa-plus-circle"></i>
                                <span>Novo Serviço</span>
                            </a>
                            <a href="add_category.php" class="quick-action-btn">
                                <i class="fas fa-layer-group"></i>
                                <span>Nova Categoria</span>
                            </a>
                            <a href="system_settings.php" class="quick-action-btn">
                                <i class="fas fa-cog"></i>
                                <span>Definições</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-history me-2"></i>Actividade Recente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-feed">
                            <?php foreach ($activities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-dot"></div>
                                <div class="activity-content">
                                    <div class="activity-action"><?php echo $activity['action']; ?></div>
                                    <div class="activity-meta">
                                        <span class="activity-user"><?php echo $activity['user']; ?></span>
                                        <span class="activity-time"><?php echo $activity['time']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Categories Overview -->
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-chart-pie me-2"></i>Distribuição por Categorias
                        </h5>
                        <a href="categories_management.php" class="btn btn-outline-azul btn-sm">
                            Gerir Categorias
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="categories-grid">
                            <?php foreach ($categories_stats as $category): ?>
                            <div class="category-stat">
                                <div class="category-name"><?php echo htmlspecialchars($category['categoria_nome']); ?></div>
                                <div class="category-count"><?php echo $category['total_servicos']; ?> serviços</div>
                                <div class="category-bar">
                                    <div class="category-progress" style="width: <?php echo ($category['total_servicos'] / max(array_column($categories_stats, 'total_servicos'))) * 100; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

