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
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        // Validation
        $errors = [];
        
        // Validate nome
        if (empty($nome)) {
            $errors[] = "Nome é obrigatório.";
        } elseif (strlen($nome) > 200) {
            $errors[] = "Nome não pode ter mais de 200 caracteres.";
        }
        
        // Validate email
        if (empty($email)) {
            $errors[] = "Email é obrigatório.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Formato de email inválido.";
        } elseif (strlen($email) > 100) {
            $errors[] = "Email não pode ter mais de 100 caracteres.";
        }
        
        // Validate password with enhanced security requirements
        if (empty($password)) {
            $errors[] = "Password é obrigatória.";
        } else {
            // Minimum length
            if (strlen($password) < 8) {
                $errors[] = "Password deve ter pelo menos 8 caracteres.";
            }
            
            // Maximum length (to prevent DOS attacks)
            if (strlen($password) > 128) {
                $errors[] = "Password não pode ter mais de 128 caracteres.";
            }
            
            // Check for uppercase letter
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = "Password deve conter pelo menos uma letra maiúscula.";
            }
            
            // Check for lowercase letter
            if (!preg_match('/[a-z]/', $password)) {
                $errors[] = "Password deve conter pelo menos uma letra minúscula.";
            }
            
            // Check for number
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = "Password deve conter pelo menos um número.";
            }
            
            // Check for special character
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $password)) {
                $errors[] = "Password deve conter pelo menos um caracter especial (!@#$%^&*()_+-=[]{}etc).";
            }
            
            // Check for common passwords
            $common_passwords = [
                'password', 'password123', '12345678', 'qwerty123', 
                'abc123', 'monkey', '1234567890', 'letmein',
                'trustno1', 'dragon', 'baseball', 'iloveyou',
                'master', 'sunshine', 'ashley', 'bailey'
            ];
            
            if (in_array(strtolower($password), $common_passwords)) {
                $errors[] = "Password é muito comum. Escolha uma password mais segura.";
            }
            
            // Check if password contains the user's name or email
            if (!empty($nome) && stripos($password, $nome) !== false) {
                $errors[] = "Password não deve conter o seu nome.";
            }
            
            if (!empty($email)) {
                $email_username = explode('@', $email)[0];
                if (stripos($password, $email_username) !== false) {
                    $errors[] = "Password não deve conter partes do seu email.";
                }
            }
        }
        
        // Check if email already exists
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id_Utilizadores FROM utilizadores WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Este email já está registado.";
            }
        }
        
        // If no errors, proceed with registration
        if (empty($errors)) {
            
            // Hash the password with stronger options
            $hashed_password = password_hash($password, PASSWORD_ARGON2ID, [
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 3
            ]);
            
            // Fallback to bcrypt if Argon2id is not available
            if ($hashed_password === false) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            }
            
            // Default user type (1 = utilizador based on your tipos table)
            $ref_id_Tipos = 1;
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO utilizadores (nome, email, password, ref_id_Tipos, inicio) 
                                   VALUES (:nome, :email, :password, 1, NOW())");
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                // Registration successful - clear form data
                unset($_SESSION['form_data']);
                $_SESSION['success_message'] = "Registo efetuado com sucesso! Pode agora fazer login.";
                
                // Redirect to login page or success page
                header("Location: ../login.php");
                exit();
                
            } else {
                $errors[] = "Erro ao registar utilizador. Tente novamente.";
            }
        }
        
        // If there are errors, store them in session and redirect back
        if (!empty($errors)) {
            $_SESSION['registration_errors'] = $errors;
            // Store form data to repopulate fields (don't store password for security)
            $_SESSION['form_data'] = [
                'nome' => $nome,
                'email' => $email
            ];
            
            // Redirect back to registration form
            header("Location: ../registo.php");
            exit();
        }
    }
    
} catch(PDOException $e) {
    // Log error (in production, don't show detailed error messages)
    error_log("Registration error: " . $e->getMessage());
    
    $_SESSION['registration_errors'] = ["Erro interno do servidor. Tente novamente mais tarde."];
    header("Location: ../registo.php");
    exit();
}

// Close connection
$conn = null;
?>