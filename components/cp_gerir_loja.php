<?php

// Database connection
$host = 'localhost';
$dbname = 'sam';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Add the missing formatDate function
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d/m/Y H:i', strtotime($date));
}

// Check if user is logged in and is a funcionario
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'funcionario') {
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
    exit();
}

// Fix: Get funcionario_id from the correct session variable
$funcionario_id = $_SESSION['user_id']; // Changed from funcionario_id to user_id
$store_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$store_id) {
    $_SESSION['error_message'] = 'Loja não encontrada.';
    header('Location: stores.php');
    exit();
}

// Get store information - Fix: Check if funcionario belongs to this store
$stmt = $pdo->prepare("
    SELECT l.*, fl.ref_id_Funcionario 
    FROM lojas l
    LEFT JOIN funcionarios_lojas fl ON l.id_Loja = fl.ref_id_Loja
    WHERE l.id_Loja = ? AND fl.ref_id_Funcionario = ?
");
$stmt->execute([$store_id, $funcionario_id]);
$loja = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$loja) {
    $_SESSION['error_message'] = 'Não tem permissão para aceder a esta loja.';
    header('Location: stores.php');
    exit();
}

// Get current employees of this store
$stmt = $pdo->prepare("
    SELECT f.id_Funcionarios, f.nome, f.email, ft.nome as tipo_nome, fl.inicio
    FROM funcionarios f
    JOIN funcionarios_lojas fl ON f.id_Funcionarios = fl.ref_id_Funcionario
    JOIN funcionarios_tipos ft ON f.ref_id_Funcionarios_Tipos = ft.id_Funcionarios_Tipos
    WHERE fl.ref_id_Loja = ?
    ORDER BY f.nome
");
$stmt->execute([$store_id]);
$funcionarios_loja = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all funcionarios not in this store (for adding)
$stmt = $pdo->prepare("
    SELECT f.id_Funcionarios, f.nome, f.email, ft.nome as tipo_nome
    FROM funcionarios f
    JOIN funcionarios_tipos ft ON f.ref_id_Funcionarios_Tipos = ft.id_Funcionarios_Tipos
    WHERE f.id_Funcionarios NOT IN (
        SELECT fl.ref_id_Funcionario 
        FROM funcionarios_lojas fl 
        WHERE fl.ref_id_Loja = ?
    )
    ORDER BY f.nome
");
$stmt->execute([$store_id]);
$funcionarios_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Update store information
    if (isset($_POST['update_store'])) {
        $nome_loja = trim($_POST['nome_loja']);
        $descricao = trim($_POST['descricao']);
        
        $errors = [];
        
        if (empty($nome_loja)) {
            $errors[] = 'O nome da loja é obrigatório.';
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("UPDATE lojas SET nome_loja = ?, descricao = ? WHERE id_Loja = ?");
                $stmt->execute([$nome_loja, $descricao, $store_id]);
                
                $_SESSION['success_message'] = 'Informações da loja atualizadas com sucesso!';
                
                // Update local data
                $loja['nome_loja'] = $nome_loja;
                $loja['descricao'] = $descricao;
                
            } catch (PDOException $e) {
                $_SESSION['error_message'] = 'Erro ao atualizar a loja: ' . $e->getMessage();
            }
        } else {
            $_SESSION['store_errors'] = $errors;
        }
    }
    
    // Add employee to store
    if (isset($_POST['add_employee'])) {
        $funcionario_add_id = (int)$_POST['funcionario_id'];
        
        if ($funcionario_add_id > 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO funcionarios_lojas (ref_id_Funcionario, ref_id_Loja, inicio) VALUES (?, ?, NOW())");
                $stmt->execute([$funcionario_add_id, $store_id]);
                
                $_SESSION['success_message'] = 'Funcionário adicionado à loja com sucesso!';
                header("Location: store-details.php?id=$store_id");
                exit();
                
            } catch (PDOException $e) {
                $_SESSION['error_message'] = 'Erro ao adicionar funcionário: ' . $e->getMessage();
            }
        }
    }
    
    // Remove employee from store
    if (isset($_POST['remove_employee'])) {
        $funcionario_remove_id = (int)$_POST['funcionario_remove_id'];
        
        // Don't allow removing yourself
        if ($funcionario_remove_id !== $funcionario_id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM funcionarios_lojas WHERE ref_id_Funcionario = ? AND ref_id_Loja = ?");
                $stmt->execute([$funcionario_remove_id, $store_id]);
                
                $_SESSION['success_message'] = 'Funcionário removido da loja com sucesso!';
                header("Location: store-details.php?id=$store_id");
                exit();
                
            } catch (PDOException $e) {
                $_SESSION['error_message'] = 'Erro ao remover funcionário: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error_message'] = 'Não pode remover-se a si próprio da loja.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Loja - SAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
   
    <link href="styles.css" rel="stylesheet"> <!-- Your existing CSS file -->
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <!-- Page Header -->
        <div class="page-header rounded">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-store me-3"></i>
                        <?php echo htmlspecialchars($loja['nome_loja'] ?? 'Loja'); ?>
                    </h1>
                    <p class="mb-0 opacity-75">Gerir informações e funcionários da loja</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="stores.php" class="btn btn-light btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Voltar às Lojas
                    </a>
                </div>
            </div>
        </div>

        <!-- Display Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['store_errors'])): ?>
            <?php foreach ($_SESSION['store_errors'] as $error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
            <?php unset($_SESSION['store_errors']); ?>
        <?php endif; ?>

        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <!-- Store Information Card -->
                <div class="card store-card mb-4">
                    <div class="card-header">
                        <h5 class="sam mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informações da Loja
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome_loja" class="form-label">Nome da Loja *</label>
                                    <input type="text" class="form-control" id="nome_loja" name="nome_loja" 
                                           value="<?php echo htmlspecialchars($loja['nome_loja'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Place ID</label>
                                    <input type="text" class="form-control" 
                                           value="<?php echo htmlspecialchars($loja['place_id'] ?? ''); ?>" disabled>
                                    <small class="text-muted">O Place ID não pode ser alterado</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" class="form-control" 
                                           value="<?php echo $loja['lat'] ?? ''; ?>" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" class="form-control" 
                                           value="<?php echo $loja['lon'] ?? ''; ?>" disabled>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3" 
                                          placeholder="Descrição da loja..."><?php echo htmlspecialchars($loja['descricao'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Data de Registo</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo formatDate($loja['inicio'] ?? ''); ?>" disabled>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" name="update_store" class="btn btn-verde">
                                    <i class="fas fa-save me-2"></i>Guardar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Employees Management Card -->
                <div class="card store-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="sam mb-0" style="color: white;">
                            <i class="fas fa-users me-2"></i>Funcionários da Loja
                        </h5>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                            <i class="fas fa-user-plus me-1"></i>Adicionar Funcionário
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($funcionarios_loja)): ?>
                            <div class="text-center p-4">
                                <i class="fas fa-users text-muted mb-3" style="font-size: 2rem;"></i>
                                <h6 class="text-muted">Nenhum funcionário atribuído</h6>
                                <p class="text-muted">Adicione funcionários para gerir esta loja.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Tipo</th>
                                            <th>Adicionado em</th>
                                            <th width="100">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($funcionarios_loja as $funcionario): ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-user text-muted me-2"></i>
                                                    <?php echo htmlspecialchars($funcionario['nome']); ?>
                                                    <?php if ($funcionario['id_Funcionarios'] == $funcionario_id): ?>
                                                        <span class="badge bg-primary ms-2">Você</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($funcionario['email']); ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo htmlspecialchars($funcionario['tipo_nome']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($funcionario['inicio']); ?></td>
                                                <td>
                                                    <?php if ($funcionario['id_Funcionarios'] != $funcionario_id): ?>
                                                        <button class="btn btn-outline-danger btn-sm" 
                                                                onclick="confirmRemoveEmployee(<?php echo $funcionario['id_Funcionarios']; ?>, '<?php echo htmlspecialchars($funcionario['nome']); ?>')">
                                                            <i class="fas fa-user-minus"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Store Stats -->
                <div class="card shadow p-4 mb-4">
                    <h5 class="sam mb-3">Estatísticas</h5>
                    <div class="mb-3">
                        <div class="info-label">Total de Funcionários</div>
                        <div class="info-value">
                            <i class="fas fa-users text-muted me-2"></i>
                            <?php echo count($funcionarios_loja); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Funcionários Disponíveis</div>
                        <div class="info-value">
                            <i class="fas fa-user-clock text-muted me-2"></i>
                            <?php echo count($funcionarios_disponiveis); ?>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="store-status status-active">Ativa</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow p-4">
                    <h5 class="sam mb-3">Ações Rápidas</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-verde" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                            <i class="fas fa-user-plus me-2"></i>Adicionar Funcionário
                        </button>
                        <a href="stores.php" class="btn btn-outline-verde">
                            <i class="fas fa-arrow-left me-2"></i>Voltar às Lojas
                        </a>
                        <a href="perfil.php" class="btn btn-outline-verde">
                            <i class="fas fa-user me-2"></i>Meu Perfil
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title sam" style="color: white;">Adicionar Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <?php if (empty($funcionarios_disponiveis)): ?>
                            <div class="text-center p-3">
                                <i class="fas fa-users-slash text-muted mb-3" style="font-size: 2rem;"></i>
                                <h6 class="text-muted">Sem funcionários disponíveis</h6>
                                <p class="text-muted mb-0">Todos os funcionários já estão atribuídos a esta loja.</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label for="funcionario_id" class="form-label">Selecione o Funcionário</label>
                                <select class="form-select" id="funcionario_id" name="funcionario_id" required>
                                    <option value="">Escolha um funcionário...</option>
                                    <?php foreach ($funcionarios_disponiveis as $funcionario): ?>
                                        <option value="<?php echo $funcionario['id_Funcionarios']; ?>">
                                            <?php echo htmlspecialchars($funcionario['nome']); ?> 
                                            (<?php echo htmlspecialchars($funcionario['tipo_nome']); ?>) - 
                                            <?php echo htmlspecialchars($funcionario['email']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <?php if (!empty($funcionarios_disponiveis)): ?>
                            <button type="submit" name="add_employee" class="btn btn-verde">
                                <i class="fas fa-user-plus me-2"></i>Adicionar
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Employee Form (Hidden) -->
    <form id="removeEmployeeForm" method="post" style="display: none;">
        <input type="hidden" id="funcionario_remove_id" name="funcionario_remove_id">
        <input type="hidden" name="remove_employee" value="1">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Confirm remove employee
        function confirmRemoveEmployee(funcionarioId, funcionarioNome) {
            if (confirm(`Tem a certeza que pretende remover ${funcionarioNome} desta loja?`)) {
                document.getElementById('funcionario_remove_id').value = funcionarioId;
                document.getElementById('removeEmployeeForm').submit();
            }
        }
    </script>
</body>
</html>