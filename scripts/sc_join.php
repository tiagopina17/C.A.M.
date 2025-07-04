<?php

// Include the database configuration file
require_once './connections/connection.php'; // Adjust path as needed

// Start session
session_start();

try {
    $pdo = new_db_connection();
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

if (!isset($_SESSION['user_type'])) {
    header('Location: 403.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $conn = new_db_connection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['code'])) {
        die("Código não fornecido.");
    }
    $code = strtoupper(trim($_GET['code']));

    $stmt = $pdo->prepare("SELECT ref_id_Loja FROM Lojas WHERE codigo = ?");
    $stmt->execute([$code]);
    $loja = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($loja) {
        $store_id = $loja['ref_id_Loja'];
        
        // Begin transaction
        $conn->beginTransaction();
        
        // Get current user data
        $userStmt = $conn->prepare("SELECT nome, email FROM utilizadores WHERE id_Utilizadores = :user_id");
        $userStmt->bindParam(':user_id', $user_id);
        $userStmt->execute();
        $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            // Insert current user as funcionario (assuming tipo 2 = employee, adjust as needed)
            $funcStmt = $conn->prepare("INSERT INTO funcionarios (nome, email, password, ref_id_Funcionarios_Tipos, inicio) 
                                      SELECT :nome, :email, password, 2, NOW() 
                                      FROM utilizadores WHERE id_Utilizadores = :user_id");
            $funcStmt->bindParam(':nome', $userData['nome']);
            $funcStmt->bindParam(':email', $userData['email']);
            $funcStmt->bindParam(':user_id', $user_id);
            
            if ($funcStmt->execute()) {
                $funcionario_id = $conn->lastInsertId();
                
                // Insert relationship into funcionarios_lojas junction table
                $junctionStmt = $conn->prepare("INSERT INTO funcionarios_lojas (ref_id_Funcionario, ref_id_Loja, inicio) VALUES (:funcionario_id, :loja_id, NOW())");
                $junctionStmt->bindParam(':funcionario_id', $funcionario_id);
                $junctionStmt->bindParam(':loja_id', $store_id);
                
                if ($junctionStmt->execute()) {
                    // Delete user from utilizadores table
                    $deleteStmt = $conn->prepare("DELETE FROM utilizadores WHERE id_Utilizadores = :user_id");
                    $deleteStmt->bindParam(':user_id', $user_id);
                    
                    if ($deleteStmt->execute()) {
                        // Delete the used code from funcionarios_codigo table
                        $deleteCodeStmt = $conn->prepare("DELETE FROM funcionarios_codigo WHERE codigo = :code");
                        $deleteCodeStmt->bindParam(':code', $code);
                        
                        if ($deleteCodeStmt->execute()) {
                            // Commit transaction
                            $conn->commit();
                            
                            // Store user name before clearing session
                            $user_nome = isset($_SESSION['user_nome']) ? $_SESSION['user_nome'] : '';

                            // Clear all session variables
                            $_SESSION = array();

                            // Set success message
                            if (!empty($user_nome)) {
                                $_SESSION['success_message'] = "Associação à loja realizada com sucesso, " . htmlspecialchars($user_nome) . "! Faça login na sua nova conta de funcionário para continuar!";
                            } else {
                                $_SESSION['success_message'] = "Associação à loja realizada com sucesso! Faça login na sua nova conta de funcionário para continuar!";
                            }

                            // Redirect to home page
                            header("Location: /pessoal/index.php");
                            exit();
                        } else {
                            throw new Exception('Erro ao remover código usado da base de dados');
                        }
                    } else {
                        throw new Exception('Erro ao remover utilizador da tabela original');
                    }
                } else {
                    throw new Exception('Erro ao associar funcionário à loja');
                }
            } else {
                throw new Exception('Erro ao criar funcionário');
            }
        } else {
            throw new Exception('Dados do utilizador não encontrados');
        }
        
    } else {
        die("Nenhuma loja encontrada com o código fornecido.");
    }

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    die("Erro ao processar associação à loja: " . $e->getMessage());
}

?>