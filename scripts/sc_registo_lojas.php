<?php
// Database configuration
require_once '../connections/connection.php';


function loadEnv($path)
{
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Remove optional quotes
        $value = trim($value, "\"'");

        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

loadEnv(__DIR__ . '/../.env');

$apiKey = $_ENV['API_KEY']; // or getenv('API_KEY')

// Start session
session_start();



// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create connection
    $conn = new_db_connection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Get and sanitize form data
        $nomeloja = trim($_POST['nomeloja'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $place_id = trim($_POST['place_id'] ?? '');
        
        // Validation
        $errors = [];
        
        // Validate nome da loja
        if (empty($nomeloja)) {
            $errors[] = "Nome da loja é obrigatório.";
        } elseif (strlen($nomeloja) > 200) {
            $errors[] = "Nome da loja não pode ter mais de 200 caracteres.";
        }
        
        // Validate place selection
        if (empty($place_id)) {
            $errors[] = "Localização da loja é obrigatória.";
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $errors[] = "Utilizador não está autenticado.";
        }
        
        // Check if store with same name and location already exists
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("SELECT id_Loja FROM lojas WHERE nome_loja = :nome_loja AND place_id = :place_id");
                $stmt->bindParam(':nome_loja', $nomeloja);
                $stmt->bindParam(':place_id', $place_id);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $errors[] = "Uma loja com este nome já existe nesta localização.";
                }
            } catch (Exception $e) {
                $errors[] = "Erro ao verificar duplicados: " . $e->getMessage();
            }
        }
        
        if (empty($errors)) {
            // Get location details from Google Places API
            $locationData = getPlaceLocation($place_id);
            
            if ($locationData === false) {
                $errors[] = 'Erro ao obter detalhes da localização';
            } else {
                try {
                    // Begin transaction
                    $conn->beginTransaction();
                    
                    // Insert into lojas table
                    $stmt = $conn->prepare("INSERT INTO lojas (nome_loja, descricao, place_id, lat, lon, inicio) VALUES (:nome_loja, :descricao, :place_id, :lat, :lon, NOW())");
                    $stmt->bindParam(':nome_loja', $nomeloja);
                    $stmt->bindParam(':descricao', $descricao);
                    $stmt->bindParam(':place_id', $place_id);
                    $stmt->bindParam(':lat', $locationData['lat']);
                    $stmt->bindParam(':lon', $locationData['lon']);
                    
                    if ($stmt->execute()) {
                        $loja_id = $conn->lastInsertId();
                        
                        // Get current user data
                        $user_id = $_SESSION['user_id'];
                        $userStmt = $conn->prepare("SELECT nome, email FROM utilizadores WHERE id_Utilizadores = :user_id");
                        $userStmt->bindParam(':user_id', $user_id);
                        $userStmt->execute();
                        $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($userData) {
                            // Insert current user as store owner (funcionario tipo 1 = dono)
                            // UPDATED: Removed ref_id_Loja from the INSERT statement
                            $funcStmt = $conn->prepare("INSERT INTO funcionarios (nome, email, password, ref_id_Funcionarios_Tipos, inicio) 
                                                      SELECT :nome, :email, password, 1, NOW() 
                                                      FROM utilizadores WHERE id_Utilizadores = :user_id");
                            $funcStmt->bindParam(':nome', $userData['nome']);
                            $funcStmt->bindParam(':email', $userData['email']);
                            $funcStmt->bindParam(':user_id', $user_id);
                            
                            if ($funcStmt->execute()) {
                                $funcionario_id = $conn->lastInsertId();
                                
                                // NEW: Insert relationship into funcionarios_lojas junction table
                                $junctionStmt = $conn->prepare("INSERT INTO funcionarios_lojas (ref_id_Funcionario, ref_id_Loja, inicio) VALUES (:funcionario_id, :loja_id, NOW())");
                                $junctionStmt->bindParam(':funcionario_id', $funcionario_id);
                                $junctionStmt->bindParam(':loja_id', $loja_id);
                                
                                if ($junctionStmt->execute()) {
                                    // Delete user from utilizadores table
                                    $deleteStmt = $conn->prepare("DELETE FROM utilizadores WHERE id_Utilizadores = :user_id");
                                    $deleteStmt->bindParam(':user_id', $user_id);
                                    
                                    if ($deleteStmt->execute()) {
                                        // Commit transaction
                                        $conn->commit();
                                        
                                        // Store user name before clearing session
                                        $user_nome = isset($_SESSION['user_nome']) ? $_SESSION['user_nome'] : '';

                                        // Clear all session variables except what we need for the message
                                        $_SESSION = array();

                                        // Set logout message immediately (don't destroy/recreate session)
                                        if (!empty($user_nome)) {
                                            $_SESSION['logout_message'] = "Loja registada com sucesso, " . htmlspecialchars($user_nome) . "! Faça login na sua nova conta de funcionário para continuar!";
                                        } else {
                                            $_SESSION['logout_message'] = "Logout efetuado com sucesso!";
                                        }

                                        // Redirect to home page
                                        header("Location: /pessoal/index.php");
                                        exit();
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
                        throw new Exception('Erro ao registar loja na base de dados');
                    }
                    
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    $errors[] = 'Erro interno: ' . $e->getMessage();
                }
            }
        }
        
        if (!empty($errors)) {
            echo "<h2>Errors:</h2>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
            echo "<p><a href='../registo_lojas.php'>Go back</a></p>";
            exit;
        }
    }
    
} catch (Exception $e) {
    echo "<h2>Database Connection Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<p><a href='../registo_lojas.php'>Go back</a></p>";
    exit;
}

// Close connection
$conn = null;

/**
 * Get place location coordinates from Google Places Details API
 */
function getPlaceLocation($placeId) {
    $apiKey = $_ENV['API_KEY'];
    $fields = 'geometry'; // Only get location coordinates
    $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$placeId}&fields={$fields}&key={$apiKey}";
    
    // Make API request    

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; PHP cURL)');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode !== 200 || !$response) {
        return false;
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }
    
    if ($data['status'] !== 'OK') {
        return false;
    }
    
    if (!isset($data['result']['geometry']['location'])) {
        return false;
    }
    
    $location = $data['result']['geometry']['location'];
    
    return [
        'lat' => $location['lat'],
        'lon' => $location['lng']
    ];
}

?>