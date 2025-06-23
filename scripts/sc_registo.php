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
        
        // Validate password
        if (empty($password)) {
            $errors[] = "Password é obrigatória.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password deve ter pelo menos 6 caracteres.";
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
            
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Default user type (1 = utilizador based on your tipos table)
            $ref_id_Tipos = 1;
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO utilizadores (nome, email, password, ref_id_Tipos, inicio) 
                                   VALUES (:nome, :email, :password, 1, NOW())");
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                // Registration successful
                $_SESSION['success_message'] = "Registo efetuado com sucesso! Pode agora fazer login.";
                
                // Redirect to login page or success page
                header("Location: ../login.php"); // Adjust path as needed
                exit();
                
            } else {
                $errors[] = "Erro ao registar utilizador. Tente novamente.";
            }
        }
        
        // If there are errors, store them in session and redirect back
        if (!empty($errors)) {
            $_SESSION['registration_errors'] = $errors;
            $_SESSION['form_data'] = [
                'nome' => $nome,
                'email' => $email
            ];
            
            // Redirect back to registration form
            header("Location: ../registo.php"); // Adjust path as needed
            exit();
        }
    }
    
} catch(PDOException $e) {
    // Log error (in production, don't show detailed error messages)
    error_log("Registration error: " . $e->getMessage());
    
    $_SESSION['registration_errors'] = ["Erro interno do servidor. Tente novamente mais tarde."];
    header("Location: ../registo.php"); // Adjust path as needed
    exit();
}

// Close connection
$conn = null;
?>