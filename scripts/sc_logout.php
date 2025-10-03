<?php
// Start session
session_start();

// Store user name before clearing session
$user_nome = isset($_SESSION['user_nome']) ? $_SESSION['user_nome'] : '';

// Clear all session variables except what we need for the message
$_SESSION = array();

// Set logout message immediately (don't destroy/recreate session)
if (!empty($user_nome)) {
    $_SESSION['logout_message'] = "Logout efetuado com sucesso. Até breve, " . htmlspecialchars($user_nome) . "!";
} else {
    $_SESSION['logout_message'] = "Logout efetuado com sucesso!";
}


// Redirect to home page
header("Location: /index.php");
exit();
?>