<?php

// Include the database configuration file
require_once './connections/connection.php'; // Adjust path as needed

try {
    $pdo = new_db_connection();
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'funcionario') {
    header('Location: 403.php');
    exit();
}

$funcionario_id = $_SESSION['user_id'];
$store_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$store_id) {
    $_SESSION['error_message'] = 'Loja não encontrada.';
    header('Location: info_lojas.php');
    exit();
}

// Get store information - Check if funcionario belongs to this store
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
    header('Location: 403.php');
    exit();
}

// Add the missing formatDate function
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d/m/Y H:i', strtotime($date));
}

// Function to generate unique 7-character code
function generateUniqueCode($pdo, $store_id) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    do {
        $code = '';
        for ($i = 0; $i < 7; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        // Check if code already exists for this store
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM funcionarios_codigo WHERE codigo = ? AND ref_id_Loja = ?");
        $stmt->execute([$code, $store_id]);
        $exists = $stmt->fetchColumn() > 0;
        
    } while ($exists);
    
    return $code;
}

// Handle AJAX requests first, before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    // Clear any output buffer to prevent HTML from being sent
    if (ob_get_level()) {
        ob_clean();
    }
    
    // Generate employee code (AJAX request)
    if (isset($_POST['generate_code'])) {
        header('Content-Type: application/json');
        
        try {
            $unique_code = generateUniqueCode($pdo, $store_id);
            
            // Insert the code into funcionarios_codigo table with store ID
            $stmt = $pdo->prepare("INSERT INTO funcionarios_codigo (ref_id_Loja, codigo) VALUES (?, ?)");
            $stmt->execute([$store_id, $unique_code]);
            
            echo json_encode(['success' => true, 'code' => $unique_code]);
            exit();
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Erro interno: ' . $e->getMessage()]);
            exit();
        }
    }
    
    // If we reach here, it's an unknown AJAX request
    echo json_encode(['success' => false, 'error' => 'Pedido AJAX inválido']);
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

// Get all codes for this store
$stmt = $pdo->prepare("
    SELECT codigo
    FROM funcionarios_codigo 
    WHERE ref_id_Loja = ? 
");
$stmt->execute([$store_id]);
$all_codes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    <a href="info_lojas.php" class="btn btn-light btn-lg">
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
                        <h5 class=" mb-0">
                            <i class="fas fa-info-circle me-2 "></i>Informações da Loja
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
                                <button type="submit" name="update_store" class="btn btn-verde text-white fw-semibold">
                                    <i class="fas fa-save me-2"></i>Guardar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Employees Management Card -->
                <div class="card store-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class=" mb-0" style="color: white;">
                            <i class="fas fa-users me-2"></i>Funcionários da Loja
                        </h5>
                        <div>
                            <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#viewCodesModal">
                                <i class="fas fa-list me-1"></i>Ver Códigos
                            </button>
                            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                                <i class="fas fa-user-plus me-1"></i>Adicionar Funcionário
                            </button>
                        </div>
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
                        <div class="info-label">Códigos Gerados</div>
                        <div class="info-value">
                            <i class="fas fa-key text-muted me-2"></i>
                            <?php echo count($all_codes); ?>
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
                        <a href="info_lojas.php" class="btn btn-outline-verde">
                            <i class="fas fa-arrow-left me-2"></i>Voltar às Lojas
                        </a>
                        <button class="btn btn-outline-verde" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                            <i class="fas fa-user-plus me-2"></i>Adicionar Funcionário
                        </button>
                        <button class="btn btn-outline-verde" data-bs-toggle="modal" data-bs-target="#viewCodesModal">
                            <i class="fas fa-list me-2"></i>Ver Todos os Códigos
                        </button>
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
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Gerar Código para Funcionário
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="generateCodeSection" class="text-center mb-4">
                        <p class="mb-3">Clique no botão abaixo para gerar um novo código único que o funcionário pode usar para se associar a esta loja.</p>
                        <button type="button" class="btn btn-verde text-dark" onclick="generateNewCode()">
                            <i class="fas fa-magic me-2"></i>Gerar Novo Código
                        </button>
                    </div>
                    
                    <div id="codeDisplaySection" style="display: none;">
                        <div class="text-center">
                            <h4 class="mb-3">Novo Código Gerado</h4>
                            <div class="bg-light p-3 rounded mb-3">
                                <h2 class="text-dark font-monospace mb-0" id="generatedCode"></h2>
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Este código expira em 7 dias</strong>
                            </div>
                            <p class="mb-3">Partilhe este código com o funcionário para que se possa juntar à loja.</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="copyCode()">
                                <i class="fas fa-copy me-1"></i>Copiar Código
                            </button>
                        </div>
                    </div>
                    
                    <div id="loadingSection" style="display: none;" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">A gerar código...</span>
                        </div>
                        <p class="mt-2">A gerar código...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View All Codes Modal -->
    <div class="modal fade" id="viewCodesModal" tabindex="-1" aria-labelledby="viewCodesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewCodesModalLabel">
                        <i class="fas fa-list me-2"></i>Todos os Códigos da Loja
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($all_codes)): ?>
                        <div class="text-center p-4">
                            <i class="fas fa-key text-muted mb-3" style="font-size: 2rem;"></i>
                            <h6 class="text-muted">Nenhum código gerado</h6>
                            <p class="text-muted">Ainda não foram gerados códigos para esta loja.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_codes as $code): ?>
                                        <tr>
                                            <td>
                                                <code class="bg-light p-1 rounded"><?php echo htmlspecialchars($code['codigo']); ?></code>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm" 
                                                        onclick="copyCodeFromTable('<?php echo $code['codigo']; ?>')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
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

        // Reset modal when it's opened
        document.getElementById('addEmployeeModal').addEventListener('show.bs.modal', function () {
            document.getElementById('generateCodeSection').style.display = 'block';
            document.getElementById('codeDisplaySection').style.display = 'none';
            document.getElementById('loadingSection').style.display = 'none';
        });

        // Generate new code via AJAX
        function generateNewCode() {
            document.getElementById('generateCodeSection').style.display = 'none';
            document.getElementById('loadingSection').style.display = 'block';
            
            const formData = new FormData();
            formData.append('generate_code', '1');
            formData.append('ajax', '1');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('loadingSection').style.display = 'none';
                
                if (data.success) {
                    document.getElementById('generatedCode').textContent = data.code;
                    document.getElementById('codeDisplaySection').style.display = 'block';
                } else {
                    alert('Erro ao gerar código: ' + (data.error || 'Erro desconhecido'));
                    document.getElementById('generateCodeSection').style.display = 'block';
                }
            })
            .catch(error => {
                document.getElementById('loadingSection').style.display = 'none';
                console.error('Error:', error);
                alert('Erro ao gerar código: ' + error.message);
                document.getElementById('generateCodeSection').style.display = 'block';
            });
        }


        // Confirm remove employee
        function confirmRemoveEmployee(funcionarioId, funcionarioNome) {
            if (confirm(`Tem a certeza que pretende remover ${funcionarioNome} desta loja?`)) {
                document.getElementById('funcionario_remove_id').value = funcionarioId;
                document.getElementById('removeEmployeeForm').submit();
            }
        }



        // Copy code from table
        // Copy code from table
function copyCodeFromTable(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Show temporary feedback
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(function() {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
    }).catch(function(err) {
        console.error('Erro ao copiar código: ', err);
    });
}

    
        // Copy code to clipboard
        function copyCode() {
            const codeElement = document.getElementById('generatedCode');
            if (codeElement) {
                const code = codeElement.textContent;
                navigator.clipboard.writeText(code).then(function() {
                    // Show temporary feedback
                    const button = event.target.closest('button');
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check me-1"></i>Copiado!';
                    button.classList.remove('btn-outline-primary');
                    button.classList.add('btn-success');
                    
                    setTimeout(function() {
                        button.innerHTML = originalText;
                        button.classList.remove('btn-success');
                        button.classList.add('btn-outline-primary');
                    }, 2000);
                }).catch(function(err) {
                    console.error('Erro ao copiar código: ', err);
                });
            }
        }

    </script>
</body>
</html>