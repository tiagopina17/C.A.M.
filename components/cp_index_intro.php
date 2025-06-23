<?php


require_once './connections/connection.php';
$conn = new_db_connection();

try {
    $query = 'SELECT categorias.nome AS categoria_nome, servicos.nome AS servico_nome, servicos.capa AS servico_capa FROM categorias INNER JOIN servicos ON id_Categorias = ref_id_Categorias';
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Initialize an array to store categories and their services
    $categories = array();
    
    // Initialize an array to store unique services for the datalist
    $unique_services = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categoria_nome = $row['categoria_nome'];
        $servico_nome = $row['servico_nome'];
        $servico_capa = $row['servico_capa'];
        
        // Check if the category already exists in the array
        if (!isset($categories[$categoria_nome])) {
            // If not, initialize the category with an empty array
            $categories[$categoria_nome] = array();
        }
        // Add the service and its capa to the category's array
        $categories[$categoria_nome][] = array('nome' => $servico_nome, 'capa' => $servico_capa);

        // Add the service to the unique services array if not already added
        if (!in_array($servico_nome, $unique_services)) {
            $unique_services[] = $servico_nome;
        }
    }
    
} catch(PDOException $e) {
    // Log error and set empty arrays to prevent errors in the rest of the code
    error_log("Database error: " . $e->getMessage());
    $categories = array();
    $unique_services = array();
}

// Display logout message if it exists
if (isset($_SESSION['logout_message'])) {
    echo '<div class="container mt-3">';
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['logout_message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    echo '</div>';
    unset($_SESSION['logout_message']);
} else {
    // Debug: Show that we didn't find the message
    echo '<!-- DEBUG: No logout message found in session -->';
}

// Display other success messages (like registration success)
if (isset($_SESSION['success_message'])) {
    echo '<div class="container mt-3">';
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['success_message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    echo '</div>';
    unset($_SESSION['success_message']);
}

// Display error messages if any
if (isset($_SESSION['error_message'])) {
    echo '<div class="container mt-3">';
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['error_message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    echo '</div>';
    unset($_SESSION['error_message']);
}

// Output the datalist with all unique services
echo '<div class="hero-section">';
echo '    <div class="hero-text ">
        <h1 class="text-dark">Bem vindo ao <span class="sam">S.A.M</span></h1>
        <p class="text-dark">A base de dados de especialistas em Portugal, para problemas que o ChatGPT não resolve. </p>        
        
    </div>
    
    <div class="hero-image">
        <img src="images/confused2.png" class="img-fluid" alt="Illustration of working people">
        <p class="text-end"><a href="https://www.freepik.com/free-vector/confused-person-sitting-with-laptop-question-marks-puzzled-young-man-thinking-about-answer-flat-vector-illustration-faq-research-stress-concept-banner-website-design-landing-web-page_26876776.htm#fromView=search&page=1&position=14&uuid=25573afd-9bee-4663-8d84-7569f0c36473">Image by pch.vector on Freepik</a></p>
    </div>
    ';

// New row with image on left and text on right
echo '    <div class="hero-row">
        <div class="hero-image">
            <img src="images/working.png" class="" alt="Description of image">
                    <p class="text-start"><a href="https://www.freepik.com/free-vector/household-renovation-professions-set_9924535.htm#fromView=search&page=1&position=2&uuid=688a5a31-ea3f-4b20-b692-d1f5af646888">Image by freepik</a></p>

        </div>
        <div class="hero-text ">
            <h1 class="text-dark">A solução para si</h1>
            <p class="text-dark">Encontre o especialista ideal para o seu problema.<br></p>
            <p class="text-dark">Começe já a pesquisar entre milhares de especialistas!</p>
    
        </div>
    </div>
    ';

echo '</div>';

// Update the buttons based on login status
echo '
 <h1 class="text-center text-dark fw-bold">Não espere mais:</h1>

<div class="text-center mt-5">
    <div class="row justify-content-center ">
        <div class="col-auto px-5">
            <a href="#" class="btn btn-outline-azul fs-4" style="padding: 14px 20px; ">Aprenda mais</a>
        </div>
        <div class="col-auto px-5">
            <a href="#" class="btn btn-outline-azul fs-4" style="padding: 14px 20px; ">Explore os serviços</a>
        </div>
        <div class="col-auto px-5   ">';

// Show different button based on login status
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo '<a href="perfil.php" class="btn btn-outline-azul fs-4" style="padding: 14px 20px;">Meu Perfil</a>';
} else {
    echo '<a href="registo.php" class="btn btn-outline-azul fs-4" style="padding: 14px 20px;">Registe-se</a>';
}

echo '        </div>
    </div>
</div>
';

// Close connection
$conn = null;
?>