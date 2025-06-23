<?php
// Database configuration
require_once '../connections/connection.php';


// Start session
session_start();

try {
    // Create connection
    $conn = new_db_connection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Get and sanitize form data
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        // Validation
        $errors = [];
        
        // Validate email
        if (empty($email)) {
            $errors[] = "Email é obrigatório.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Formato de email inválido.";
        }
        
        // Validate password
        if (empty($password)) {
            $errors[] = "Password é obrigatória.";
        }
        
        // If no validation errors, proceed with login
        if (empty($errors)) {
            
            // Get user data from database including user type
            $stmt = $conn->prepare("
                SELECT u.id_Utilizadores, u.nome, u.email, u.password, u.imgperfil, u.ref_id_Tipos, t.nome as tipo_nome
                FROM utilizadores u
                INNER JOIN tipos t ON u.ref_id_Tipos = t.id_Tipos
                WHERE u.email = :email
            ");
            
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    
                    // Password is correct, create session
                    $_SESSION['user_id'] = $user['id_Utilizadores'];
                    $_SESSION['user_nome'] = $user['nome'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_imgperfil'] = $user['imgperfil'];
                    $_SESSION['user_tipo_id'] = $user['ref_id_Tipos'];
                    $_SESSION['user_tipo_nome'] = $user['tipo_nome'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_type'] = 'user'; // To distinguish from regular users

                    
                    // Set success message
                    $_SESSION['success_message'] = "Login efetuado com sucesso! Bem-vindo, " . $user['nome'] . "!";
                    
                    // Redirect based on user type
                    switch($user['ref_id_Tipos']) {
                        case 4: // administrador
                            header("Location: ../administracao");
                            break;
                        case 3: // moderador
                            header("Location: ../administracao");
                            break;
                        case 1: // utilizador
                        default:
                            header("Location: ../"); // or main page
                            break;
                    }
                    exit();
                    
                } else {
                    $errors[] = "Email ou password incorretos.";
                }
                
            } else {
                $errors[] = "Email ou password incorretos.";
            }
        }
        
        // If there are errors, store them in session and redirect back
        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_email'] = $email; // Keep email for user convenience
            
            // Redirect back to login form
            header("Location: ../login.php"); // Adjust path as needed
            exit();
        }
    }
    
} catch(PDOException $e) {
    // Log error (in production, don't show detailed error messages)
    error_log("Login error: " . $e->getMessage());
    
    $_SESSION['login_errors'] = ["Erro interno do servidor. Tente novamente mais tarde."];
    header("Location: ../login.php"); // Adjust path as needed
    exit();
}

// Close connection
$conn = null;

// If accessed directly without POST, redirect to login page
header("Location: ../login.php");
exit();
?>