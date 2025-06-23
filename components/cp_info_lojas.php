<?php

// Check if user is logged in and is a funcionario
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'funcionario') {
    header('Location: login.php');
    exit();
}

// Include the database configuration file
require_once './connections/connection.php'; // Adjust path as needed

try {
    $pdo = new_db_connection();
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$funcionario_id = $_SESSION['user_id'];

// Get funcionario info
$stmt = $pdo->prepare("
    SELECT f.*, ft.nome as tipo_nome 
    FROM funcionarios f 
    JOIN funcionarios_tipos ft ON f.ref_id_Funcionarios_Tipos = ft.id_Funcionarios_Tipos 
    WHERE f.id_Funcionarios = ?
");
$stmt->execute([$funcionario_id]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$funcionario) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_store'])) {
        $nome_loja = trim($_POST['nome_loja']);
        $descricao = trim($_POST['descricao']);
        $place_id = trim($_POST['place_id']);
        $lat = floatval($_POST['lat']);
        $lon = floatval($_POST['lon']);
        
        $errors = [];
        if (empty($nome_loja)) $errors[] = "O nome da loja é obrigatório.";
        if (empty($place_id)) $errors[] = "A localização é obrigatória.";
        if ($lat == 0 || $lon == 0) $errors[] = "Coordenadas válidas são obrigatórias.";
        
        // Check if store name already exists
        $stmt = $pdo->prepare("SELECT id_Loja FROM lojas WHERE nome_loja = ?");
        $stmt->execute([$nome_loja]);
        if ($stmt->fetch()) {
            $errors[] = "Já existe uma loja com este nome.";
        }
        
        if (empty($errors)) {
            try {
                $pdo->beginTransaction();
                
                // Insert new store
                $stmt = $pdo->prepare("
                    INSERT INTO lojas (nome_loja, descricao, place_id, lat, lon, inicio) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$nome_loja, $descricao, $place_id, $lat, $lon]);
                $loja_id = $pdo->lastInsertId();
                
                // Associate store with funcionario
                $stmt = $pdo->prepare("
                    INSERT INTO funcionarios_lojas (ref_id_Funcionario, ref_id_Loja) VALUES (?, ?)
                ");
                $stmt->execute([$funcionario_id, $loja_id]);
                
                $pdo->commit();
                $_SESSION['success_message'] = "Loja adicionada com sucesso!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['error_message'] = "Erro ao adicionar loja: " . $e->getMessage();
            }
        } else {
            $_SESSION['store_errors'] = $errors;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['update_store'])) {
        $loja_id = $_POST['loja_id'];
        $nome_loja = trim($_POST['nome_loja']);
        $descricao = trim($_POST['descricao']);
        $place_id = trim($_POST['place_id']);
        $lat = floatval($_POST['lat']);
        $lon = floatval($_POST['lon']);
        
        $errors = [];
        if (empty($nome_loja)) $errors[] = "O nome da loja é obrigatório.";
        if (empty($place_id)) $errors[] = "A localização é obrigatória.";
        if ($lat == 0 || $lon == 0) $errors[] = "Coordenadas válidas são obrigatórias.";
        
        // Check if funcionario has access to this store
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM funcionarios_lojas 
            WHERE ref_id_Funcionario = ? AND ref_id_Loja = ?
        ");
        $stmt->execute([$funcionario_id, $loja_id]);
        if ($stmt->fetchColumn() == 0) {
            $errors[] = "Não tem permissão para editar esta loja.";
        }
        
        // Check if store name already exists (excluding current store)
        $stmt = $pdo->prepare("SELECT id_Loja FROM lojas WHERE nome_loja = ? AND id_Loja != ?");
        $stmt->execute([$nome_loja, $loja_id]);
        if ($stmt->fetch()) {
            $errors[] = "Já existe uma loja com este nome.";
        }
        
        if (empty($errors)) {
            $stmt = $pdo->prepare("
                UPDATE lojas SET nome_loja = ?, descricao = ?, place_id = ?, lat = ?, lon = ? 
                WHERE id_Loja = ?
            ");
            if ($stmt->execute([$nome_loja, $descricao, $place_id, $lat, $lon, $loja_id])) {
                $_SESSION['success_message'] = "Loja atualizada com sucesso!";
            } else {
                $_SESSION['error_message'] = "Erro ao atualizar loja.";
            }
        } else {
            $_SESSION['store_errors'] = $errors;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['remove_store'])) {
        $loja_id = $_POST['loja_id'];
        
        // Check if funcionario has access to this store
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM funcionarios_lojas 
            WHERE ref_id_Funcionario = ? AND ref_id_Loja = ?
        ");
        $stmt->execute([$funcionario_id, $loja_id]);
        
        if ($stmt->fetchColumn() > 0) {
            try {
                $pdo->beginTransaction();
                
                // Remove association
                $stmt = $pdo->prepare("
                    DELETE FROM funcionarios_lojas 
                    WHERE ref_id_Funcionario = ? AND ref_id_Loja = ?
                ");
                $stmt->execute([$funcionario_id, $loja_id]);
                
                // Check if store has other funcionarios associated
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM funcionarios_lojas WHERE ref_id_Loja = ?");
                $stmt->execute([$loja_id]);
                
                if ($stmt->fetchColumn() == 0) {
                    // If no other funcionarios, delete the store
                    $stmt = $pdo->prepare("DELETE FROM lojas WHERE id_Loja = ?");
                    $stmt->execute([$loja_id]);
                }
                
                $pdo->commit();
                $_SESSION['success_message'] = "Loja removida com sucesso!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['error_message'] = "Erro ao remover loja: " . $e->getMessage();
            }
        } else {
            $_SESSION['error_message'] = "Não tem permissão para remover esta loja.";
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get stores managed by this funcionario
$stmt = $pdo->prepare("
    SELECT l.*, fl.inicio as data_associacao
    FROM lojas l
    JOIN funcionarios_lojas fl ON l.id_Loja = fl.ref_id_Loja
    WHERE fl.ref_id_Funcionario = ?
    ORDER BY l.nome_loja ASC
");
$stmt->execute([$funcionario_id]);
$lojas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$total_lojas = count($lojas);
$lojas_este_mes = 0;
$current_month = date('Y-m');

foreach ($lojas as $loja) {
    if (date('Y-m', strtotime($loja['inicio'])) === $current_month) {
        $lojas_este_mes++;
    }
}

// Helper functions
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function timeAgo($date) {
    $now = new DateTime();
    $past = new DateTime($date);
    $diff = $now->diff($past);
    
    if ($diff->y > 0) return $diff->y . ' ano' . ($diff->y > 1 ? 's' : '') . ' atrás';
    if ($diff->m > 0) return $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '') . ' atrás';
    if ($diff->d > 0) return $diff->d . ' dia' . ($diff->d > 1 ? 's' : '') . ' atrás';
    if ($diff->h > 0) return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
    if ($diff->i > 0) return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
    return 'Agora mesmo';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Lojas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
   
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <!-- Page Header -->
        <div class="page-header rounded">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2"><i class="fas fa-store me-3"></i>Gestão de Lojas</h1>
                    <p class="mb-0 opacity-75">Gerir as suas lojas e informações relacionadas</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                        <i class="fas fa-plus me-2"></i>Adicionar Loja
                    </button>
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
            <div class="col-md-9">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card stats-card text-white p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $total_lojas; ?></h3>
                                    <p class="mb-0 opacity-75">Total de Lojas</p>
                                </div>
                                <i class="fas fa-store" style="font-size: 2rem; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-info text-white p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $lojas_este_mes; ?></h3>
                                    <p class="mb-0 opacity-75">Adicionadas Este Mês</p>
                                </div>
                                <i class="fas fa-calendar-plus" style="font-size: 2rem; opacity: 0.7;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stores Grid -->
                <div class="row">
                    <?php if (empty($lojas)): ?>
                        <div class="col-12">
                            <div class="card text-center p-5">
                                <i class="fas fa-store text-muted mb-3" style="font-size: 3rem;"></i>
                                <h4 class="text-muted mb-3">Nenhuma loja encontrada</h4>
                                <p class="text-muted mb-4">Comece por adicionar a sua primeira loja.</p>
                                <button class="btn btn-verde" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                                    <i class="fas fa-plus me-2"></i>Adicionar Primeira Loja
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($lojas as $loja): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card store-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="sam mb-0"><?php echo htmlspecialchars($loja['nome_loja']); ?></h5>
                                            <span class="store-status status-active">Ativa</span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="info-label">Place ID</div>
                                            <div class="info-value">
                                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                                <?php echo htmlspecialchars($loja['place_id']); ?>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="info-label">Coordenadas</div>
                                            <div class="info-value">
                                                <i class="fas fa-globe text-muted me-2"></i>
                                                <?php echo $loja['lat']; ?>, <?php echo $loja['lon']; ?>
                                            </div>
                                        </div>

                                        <?php if ($loja['descricao']): ?>
                                        <div class="mb-3">
                                            <div class="info-label">Descrição</div>
                                            <div class="info-value"><?php echo htmlspecialchars($loja['descricao']); ?></div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <div class="info-label">Data de Registo</div>
                                            <div class="info-value">
                                                <i class="fas fa-calendar text-muted me-2"></i>
                                                <?php echo formatDate($loja['inicio']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-verde btn-sm flex-fill"                                                      
                                                        onclick="window.location.href='gerir_loja.php?id=<?php echo $loja['id_Loja']; ?>'">                                                 
                                                    <i class="fas fa-edit me-1"></i>Editar                                             
                                                </button>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmRemoveStore(<?php echo $loja['id_Loja']; ?>, '<?php echo htmlspecialchars($loja['nome_loja']); ?>')">
                                                <i class="fas fa-trash me-1"></i>Remover
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-3">
                <!-- Quick Actions -->
                <div class="card shadow p-4 mb-4">
                    <h5 class="sam mb-3">Ações Rápidas</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-verde" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                            <i class="fas fa-plus me-2"></i>Nova Loja
                        </button>
                        <a href="perfil.php" class="btn btn-outline-verde">
                            <i class="fas fa-user me-2"></i>Meu Perfil
                        </a>
                        <a href="help.php" class="btn btn-outline-verde">
                            <i class="fas fa-question-circle me-2"></i>Ajuda
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                        </a>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card shadow p-4">
                    <h5 class="sam mb-3">Informações</h5>
                    <div class="mb-3">
                        <div class="info-label">Funcionário</div>
                        <div class="info-value"><?php echo htmlspecialchars($funcionario['nome']); ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Tipo</div>
                        <div class="info-value"><?php echo htmlspecialchars($funcionario['tipo_nome']); ?></div>
                    </div>
                    <div class="mb-0">
                        <div class="info-label">Lojas Geridas</div>
                        <div class="info-value"><?php echo $total_lojas; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Store Modal -->
    <div class="modal fade" id="addStoreModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title sam">Adicionar Nova Loja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome_loja" class="form-label">Nome da Loja *</label>
                                <input type="text" class="form-control" id="nome_loja" name="nome_loja" required>
                                <div class="invalid-feedback">Este campo é obrigatório.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="place_id" class="form-label">Place ID *</label>
                                <input type="text" class="form-control" id="place_id" name="place_id" required>
                                <div class="invalid-feedback">Este campo é obrigatório.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lat" class="form-label">Latitude *</label>
                                <input type="number" step="any" class="form-control" id="lat" name="lat" required>
                                <div class="invalid-feedback">Este campo é obrigatório.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lon" class="form-label">Longitude *</label>
                                <input type="number" step="any" class="form-control" id="lon" name="lon" required>
                                <div class="invalid-feedback">Este campo é obrigatório.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Descrição opcional da loja..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_store" class="btn btn-verde">
                            <i class="fas fa-save me-2"></i>Adicionar Loja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

   

    <!-- Remove Store Modal -->
    <div class="modal fade" id="removeStoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Confirmar Remoção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center">Tem a certeza que pretende remover a loja <strong id="remove_store_name"></strong>?</p>
                    <p class="text-center text-muted"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <input type="hidden" id="remove_loja_id" name="loja_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="remove_store" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Remover Loja
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Bootstrap JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
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

        // Edit store function
          // Edit store function
        function editStore(storeData) {
            document.getElementById('edit_loja_id').value = storeData.id_Loja;
            document.getElementById('edit_nome_loja').value = storeData.nome_loja;

            document.getElementById('edit_descricao').value = storeData.descricao || '';
            
            // Show the edit modal
            var editModal = new bootstrap.Modal(document.getElementById('editStoreModal'));
            editModal.show();
        }

        // Confirm remove store function
        function confirmRemoveStore(lojaId, lojaName) {
            document.getElementById('remove_loja_id').value = lojaId;
            document.getElementById('remove_store_name').textContent = lojaName;
            
            // Show the remove modal
            var removeModal = new bootstrap.Modal(document.getElementById('removeStoreModal'));
            removeModal.show();
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (alert.querySelector('.btn-close')) {
                        var bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });

        // Clear form when add modal is closed
        document.getElementById('addStoreModal').addEventListener('hidden.bs.modal', function () {
            this.querySelector('form').reset();
            this.querySelector('form').classList.remove('was-validated');
        });

        // Clear form when edit modal is closed
        document.getElementById('editStoreModal').addEventListener('hidden.bs.modal', function () {
            this.querySelector('form').classList.remove('was-validated');
        });
    </script>
</body>
</html>