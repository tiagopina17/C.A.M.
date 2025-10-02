<?php

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
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

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type']; // Should be 'user' or 'funcionario'

// Validate user type
if (!in_array($user_type, ['user', 'funcionario'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Fetch user data based on user type
if ($user_type === 'funcionario') {
    // UPDATED: Use junction table to get loja information
    $stmt = $pdo->prepare("
        SELECT f.*, ft.nome as tipo_nome, 
               GROUP_CONCAT(l.nome_loja SEPARATOR ', ') as lojas_nomes,
               COUNT(DISTINCT fl.ref_id_Loja) as total_lojas
        FROM funcionarios f 
        JOIN funcionarios_tipos ft ON f.ref_id_Funcionarios_Tipos = ft.id_Funcionarios_Tipos 
        LEFT JOIN funcionarios_lojas fl ON f.id_Funcionarios = fl.ref_id_Funcionario
        LEFT JOIN lojas l ON fl.ref_id_Loja = l.id_Loja
        WHERE f.id_Funcionarios = ?
        GROUP BY f.id_Funcionarios
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("
        SELECT u.*, t.nome as tipo_nome 
        FROM utilizadores u 
        JOIN tipos t ON u.ref_id_Tipos = t.id_Tipos 
        WHERE u.id_Utilizadores = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if user exists
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Fetch user statistics based on type
if ($user_type === 'funcionario') {
    // UPDATED: Get actual count of lojas from junction table
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_lojas FROM funcionarios_lojas WHERE ref_id_Funcionario = ?");
    $stmt->execute([$user_id]);
    $stats_lojas = $stmt->fetch(PDO::FETCH_ASSOC)['total_lojas'];
    
} else {
    

    $stmt = $pdo->prepare("SELECT COUNT(*) as total_lojas FROM utilizadores_lojas WHERE ref_id_Utilizador = ?");
    $stmt->execute([$user_id]);
    $stats_lojas = $stmt->fetch(PDO::FETCH_ASSOC)['total_lojas'];
}

// Fetch recent activity (simulated - you can customize this based on your needs)
$recent_activities = [
    ['action' => 'Alteração de perfil', 'description' => 'Atualizou as informações pessoais', 'time' => 'Há 2 horas'],
    ['action' => 'Início de sessão', 'description' => 'Acesso através de ' . $_SERVER['HTTP_USER_AGENT'], 'time' => 'Hoje'],
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        
        // Validate input
        $errors = [];
        if (empty($nome)) {
            $errors[] = "O nome é obrigatório.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email inválido.";
        }
        
        // Check if email already exists for another user
        if ($user_type === 'funcionario') {
            $stmt = $pdo->prepare("SELECT id_Funcionarios FROM funcionarios WHERE email = ? AND id_Funcionarios != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $errors[] = "Este email já está a ser utilizado por outro funcionário.";
            }
            
            // Also check if email exists in utilizadores table
            $stmt = $pdo->prepare("SELECT id_Utilizadores FROM utilizadores WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Este email já está a ser utilizado por outro utilizador.";
            }
        } else {
            $stmt = $pdo->prepare("SELECT id_Utilizadores FROM utilizadores WHERE email = ? AND id_Utilizadores != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $errors[] = "Este email já está a ser utilizado por outro utilizador.";
            }
            
            // Also check if email exists in funcionarios table
            $stmt = $pdo->prepare("SELECT id_Funcionarios FROM funcionarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Este email já está a ser utilizado por um funcionário.";
            }
        }
        
        if (empty($errors)) {
            if ($user_type === 'funcionario') {
                $stmt = $pdo->prepare("UPDATE funcionarios SET nome = ?, email = ? WHERE id_Funcionarios = ?");
            } else {
                $stmt = $pdo->prepare("UPDATE utilizadores SET nome = ?, email = ? WHERE id_Utilizadores = ?");
            }
            
            if ($stmt->execute([$nome, $email, $user_id])) {
                $_SESSION['success_message'] = "Perfil atualizado com sucesso!";
                $user['nome'] = $nome;
                $user['email'] = $email;
            } else {
                $_SESSION['error_message'] = "Erro ao atualizar o perfil.";
            }
        } else {
            $_SESSION['profile_errors'] = $errors;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $errors = [];
        
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "Password atual incorreta.";
        }
        
        if (strlen($new_password) < 6) {
            $errors[] = "A nova password deve ter pelo menos 6 caracteres.";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "As passwords não coincidem.";
        }
        
        if (empty($errors)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            if ($user_type === 'funcionario') {
                $stmt = $pdo->prepare("UPDATE funcionarios SET password = ? WHERE id_Funcionarios = ?");
            } else {
                $stmt = $pdo->prepare("UPDATE utilizadores SET password = ? WHERE id_Utilizadores = ?");
            }
            
            if ($stmt->execute([$hashed_password, $user_id])) {
                $_SESSION['success_message'] = "Password alterada com sucesso!";
            } else {
                $_SESSION['error_message'] = "Erro ao alterar a password.";
            }
        } else {
            $_SESSION['password_errors'] = $errors;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Format date for display
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

// UPDATED: Helper function to get user display info
function getUserDisplayInfo($user, $user_type) {
    $info = [
        'nome' => $user['nome'],
        'email' => $user['email'],
        'tipo' => $user['tipo_nome'],
        'inicio' => $user['inicio']
    ];
    
    if ($user_type === 'funcionario') {
        // Handle multiple lojas
        if (isset($user['lojas_nomes']) && !empty($user['lojas_nomes'])) {
            $info['lojas'] = $user['lojas_nomes']; // This will be a comma-separated string
            $info['total_lojas'] = $user['total_lojas'];
        } else {
            $info['lojas'] = 'Nenhuma loja associada';
            $info['total_lojas'] = 0;
        }
    }
    
    return $info;
}

$user_display_info = getUserDisplayInfo($user, $user_type);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Utilizador</title>
</head>
<body class="bg-light ">
    <div class="container mt-5 mb-5 ">
        <div class="row">
            <!-- Profile Header -->
            <div class="col-12">
                <div class="card shadow p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <?php if ($user['imgperfil']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($user['imgperfil']); ?>" alt="Avatar" class="profile-avatar mb-3">
                            <?php else: ?>
                                <div class="profile-avatar mb-3">
                                    <?php echo strtoupper(substr($user['nome'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <button class="btn btn-outline-verde btn-sm" onclick="document.getElementById('avatarUpload').click()">
                                    <i class="fas fa-camera me-2"></i>Alterar Foto
                                </button>
                                <input type="file" id="avatarUpload" accept="image/*" style="display: none;" onchange="uploadAvatar(this)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h2 class="sam mb-2"><?php echo htmlspecialchars($user['nome']); ?></h2>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($user['email']); ?></p>
                            <p class="text-muted mb-2">
                                <span class="user-type-badge"><?php echo htmlspecialchars(ucfirst($user['tipo_nome'])); ?></span>
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Membro desde <?php echo formatDate($user['inicio']); ?>
                            </p>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-verde" onclick="toggleEditMode()">
                                <i class="fas fa-edit me-2"></i>Editar Perfil
                            </button>
                        </div>
                    </div>
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

        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs mb-4" id="profileTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#info">
                            <i class="fas fa-user me-2"></i>Informações Pessoais
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#security">
                            <i class="fas fa-shield-alt me-2"></i>Segurança
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#activity">
                            <i class="fas fa-history me-2"></i>Atividade
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="info">
                        <div class="card shadow p-4">
                            <h4 class="sam mb-4">Informações Pessoais</h4>
                            
                            <?php if (isset($_SESSION['profile_errors'])): ?>
                                <?php foreach ($_SESSION['profile_errors'] as $error): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                                <?php unset($_SESSION['profile_errors']); ?>
                            <?php endif; ?>
                            
                            <!-- Display Mode -->
                            <div id="displayMode">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-label">Nome</div>
                                        <div class="info-value"><?php echo htmlspecialchars($user['nome']); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Email</div>
                                        <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-label">Tipo de Conta</div>
                                        <div class="info-value"><?php echo htmlspecialchars(ucfirst($user['tipo_nome'])); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Data de Registo</div>
                                        <div class="info-value"><?php echo formatDate($user['inicio']); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <div id="editMode" class="edit-mode">
                                <form method="post" class="needs-validation" novalidate>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nome" class="form-label">Nome:</label>
                                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                                            <div class="valid-feedback">Tudo certo!</div>
                                            <div class="invalid-feedback">Este campo é obrigatório.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email:</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            <div class="valid-feedback">Tudo certo!</div>
                                            <div class="invalid-feedback">Este campo é obrigatório.</div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="update_profile" class="btn btn-verde">
                                            <i class="fas fa-save me-2"></i>Guardar Alterações
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="cancelEdit()">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security">
                        <div class="card shadow p-4">
                            <h4 class="sam mb-4">Segurança da Conta</h4>
                            
                            <?php if (isset($_SESSION['password_errors'])): ?>
                                <?php foreach ($_SESSION['password_errors'] as $error): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                                <?php unset($_SESSION['password_errors']); ?>
                            <?php endif; ?>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Mantenha a sua conta segura alterando regularmente a sua password.
                            </div>

                            <form method="post" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Atual:</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Nova Password:</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                                    <div class="invalid-feedback">A password deve ter pelo menos 6 caracteres.</div>
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirmar Nova Password:</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-verde">
                                    <i class="fas fa-key me-2"></i>Alterar Password
                                </button>
                            </form>

                            <hr class="my-4">

                            <h5 class="sam mb-3">Informações da Sessão</h5>
                            <div class="card info-card p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-label">Último Acesso</div>
                                        <div class="info-value"><?php echo timeAgo($user['inicio']); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">IP Atual</div>
                                        <div class="info-value"><?php echo $_SERVER['REMOTE_ADDR']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Tab -->
                    <div class="tab-pane fade" id="activity">
                        <div class="card shadow p-4">
                            <h4 class="sam mb-4">Atividade Recente</h4>
                            
                            <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($activity['action']); ?></div>
                                        <div class="text-muted"><?php echo htmlspecialchars($activity['description']); ?></div>
                                    </div>
                                    <div class="activity-date"><?php echo htmlspecialchars($activity['time']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold">Registo na plataforma</div>
                                        <div class="text-muted">Conta criada como <?php echo htmlspecialchars($user['tipo_nome']); ?></div>
                                    </div>
                                    <div class="activity-date"><?php echo timeAgo($user['inicio']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Statistics/Management Card -->
                <?php if ($_SESSION['user_type'] == 'funcionario'): ?>
                    <!-- Management Card for Funcionario -->
                    <div class="card shadow p-4 mb-4" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none;">
                        <div class="text-center text-white">
                            <div class="mb-3">
                                <i class="fas fa-store-alt" style="font-size: 2rem; opacity: 0.9;"></i>
                            </div>
                            <h5 class="mb-3 fw-bold">Gestão de Lojas</h5>
                            <p class="mb-4 opacity-75">Gerir e administrar as suas lojas:</p>
                            <a href="info_lojas.php" class="btn btn-light btn-lg px-4 py-2 fw-semibold" style="border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                <i class="fas fa-cogs me-2"></i>Gerir Lojas
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Statistics Card for other user types -->
                    <div class="card stats-card shadow p-4 mb-4">
                        <h5 class="text-white mb-3">
                            <i class="fas fa-chart-line me-2"></i>Estatísticas
                        </h5>
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-white mb-0"><?php echo $stats_lojas; ?></h3>
                                <small class="text-white-50">Lojas</small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Quick Actions -->
                <div class="card shadow p-4 mb-4">
                    <h5 class="sam mb-3">Ações Rápidas</h5>
                    <div class="d-grid gap-2">
                        <?php if ($_SESSION['user_type'] != 'funcionario'): ?>
                        <a href="registo_lojas.php" class="btn btn-outline-verde mt-3">
                            <i class="fas fa-store me-2"></i>Registar uma loja
                        </a>
                        <!-- Button to trigger modal -->
                        <button type="button" class="btn btn-outline-verde" data-bs-toggle="modal" data-bs-target="#joinLojaModal">
                            <i class="fas fa-door-closed me-2"></i>Juntar-se a uma loja
                        </button>

                        
                        <?php endif; ?>
                        <a href="help.php" class="btn btn-outline-verde">
                            <i class="fas fa-question-circle me-2"></i>Ajuda
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Bootstrap Modal -->
                        <div class="modal fade" id="joinLojaModal" tabindex="-1" aria-labelledby="joinLojaModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="joinLojaModalLabel"><i class="fas fa-door-closed me-2"></i>Juntar-se a uma loja</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Modal content here -->
                                        <form onsubmit="event.preventDefault(); joinLojaRedirect();">
                                            <div class="mb-3">
                                                <label for="codigoLoja" class="form-label">Código</label>
                                                <input type="text" class="form-control" id="codigoLoja" placeholder="Insira o código de acesso">
                                            </div>
                                            <button type="submit" class="btn btn-outline-verde">Juntar-se</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                        function joinLojaRedirect() {
                                const code = document.getElementById('codigoLoja').value.trim();
                                if (code) {
                                        window.location.href = 'scripts/sc_join.php?code=' + encodeURIComponent(code);
                                }
                        }
                        </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let isEditMode = false;

        function toggleEditMode() {
            const displayMode = document.getElementById('displayMode');
            const editMode = document.getElementById('editMode');
            
            if (isEditMode) {
                displayMode.style.display = 'block';
                editMode.style.display = 'none';
                isEditMode = false;
            } else {
                displayMode.style.display = 'none';
                editMode.style.display = 'block';
                isEditMode = true;
            }
        }

        function cancelEdit() {
            toggleEditMode();
        }

        function uploadAvatar(input) {
            if (input.files && input.files[0]) {
                const formData = new FormData();
                formData.append('avatar', input.files[0]);
                
                fetch('upload_avatar.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao carregar a imagem: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao carregar a imagem.');
                });
            }
        }

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('As passwords não coincidem.');
            } else {
                this.setCustomValidity('');
            }
        });

        // Bootstrap validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
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
    </script>
</body>
</html>