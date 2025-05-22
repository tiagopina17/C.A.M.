<?php
// Start session
session_start();

// Check if user is actually logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    
    // Store user name for goodbye message
    $user_nome = isset($_SESSION['user_nome']) ? $_SESSION['user_nome'] : '';
    
    // Unset all session variables
    $_SESSION = array();
    
    // Delete the session cookie if it exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Start new session for logout message
    session_start();
    
    // Set logout success message
    if (!empty($user_nome)) {
        $_SESSION['logout_message'] = "Logout efetuado com sucesso. Até breve, " . htmlspecialchars($user_nome) . "!";
    } else {
        $_SESSION['logout_message'] = "Logout efetuado com sucesso!";
    }
    
} else {
    // User wasn't logged in, set appropriate message
    $_SESSION['logout_message'] = "Não estava logado.";
}

// Redirect to home page
header("Location: ../index.php");
exit();
?>