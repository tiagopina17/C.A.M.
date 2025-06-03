<?php
// Database configuration
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "sam";

// Start session
session_start();

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
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
            
            // CORRECTED: Query the funcionarios table (not utilizadores) with proper JOIN
            $stmt = $conn->prepare("
                SELECT f.id_Funcionarios, f.nome, f.email, f.password, f.imgperfil, 
                       f.ref_id_Funcionarios_Tipos, f.ref_id_Loja, ft.nome as tipo_nome
                FROM funcionarios f
                INNER JOIN funcionarios_tipos ft ON f.ref_id_Funcionarios_Tipos = ft.id_Funcionarios_Tipos
                WHERE f.email = :email
            ");
            
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    
                    // Password is correct, create session
                    $_SESSION['user_id'] = $user['id_Funcionarios'];
                    $_SESSION['user_nome'] = $user['nome'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_imgperfil'] = $user['imgperfil'];
                    $_SESSION['user_tipo_id'] = $user['ref_id_Funcionarios_Tipos'];
                    $_SESSION['user_tipo_nome'] = $user['tipo_nome'];
                    $_SESSION['user_loja_id'] = $user['ref_id_Loja'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_type'] = 'funcionario'; // To distinguish from regular users
                    
                    // Set success message
                    $_SESSION['success_message'] = "Login efetuado com sucesso! Bem-vindo, " . $user['nome'] . "!";
                    
                    // Redirect based on employee type
                    switch($user['ref_id_Funcionarios_Tipos']) {
                        case 1: // dono (owner)
                            header("Location: ../");
                            break;
                        case 2: // funcionario (employee)
                            header("Location: ../"); // or wherever employees should go
                            break;
                        default:
                            header("Location: ../");
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
            header("Location: ../login_funcionario.php");
            exit();
        }
    }
    
} catch(PDOException $e) {
    // Log error (in production, don't show detailed error messages)
    error_log("Login error: " . $e->getMessage());
    
    $_SESSION['login_errors'] = ["Erro interno do servidor. Tente novamente mais tarde."];
    header("Location: ../login_funcionario.php");
    exit();
}

// Close connection
$conn = null;

// If accessed directly without POST, redirect to login page
header("Location: ../login_funcionario.php");
exit();
?>