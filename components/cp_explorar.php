<?php
require_once './connections/connection.php';

try {
    $conn = new_db_connection();
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

echo '
<div class="mt-5">
    <h2 class="text-center  text-dark">De que serviços precisa?</h2>
</div>
<div class="form-group">
</div>';
echo '
<div class="d-flex justify-content-center">
    <div
    <div class="col-md-7 text-center">
        <div class="input-group">
            <input id="search" name="search" type="text" class="form-control fs-5" placeholder="Pesquise aqui" list="list-timezone">
            <div class="input-group-append">
                <a href="#" class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></a>
            </div>
        </div>
        <ul id="dropdown-menu" class="dropdown-menu" style="display: none;">';

// Loop through unique services and generate dropdown items
foreach ($unique_services as $service) {
    echo '<li><a class="dropdown-item" href="#">' . htmlspecialchars($service) . '</a></li>';
}

echo '
        </ul>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var searchInput = document.getElementById("search");
    var dropdownItems = document.querySelectorAll("#dropdown-menu .dropdown-item");
    var dropdownMenu = document.getElementById("dropdown-menu");

    searchInput.addEventListener("input", function() {
        var input = this.value.trim().toLowerCase();

        dropdownItems.forEach(function(item) {
            var text = item.textContent.trim().toLowerCase();
            if (text.startsWith(input)) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });

        // Show the dropdown menu if there are matching items, hide it otherwise
        var hasMatchingItems = Array.from(dropdownItems).some(function(item) {
            return item.style.display !== "none";
        });

        if (hasMatchingItems && input !== "") {
            dropdownMenu.style.display = "block";
        } else {
            dropdownMenu.style.display = "none";
        }
    });

    // Add click event listener to dropdown items
    dropdownItems.forEach(function(item) {
        item.addEventListener("click", function() {
            searchInput.value = this.textContent.trim();
            dropdownMenu.style.display = "none";
        });
    });
});
</script>';

foreach ($categories as $categoria => $servicos) {
    echo '
<section class="ftco-section">
    <div class="container">
        <div class="row d- justify-content-between align-items-end">
            <div class="col-md-6 col-10 mt-5">
                <h2 class="text-dark">' . htmlspecialchars($categoria) . '</h2>
            </div>
            <div class="col-md-6 col-2 mt-3 text-right">
                <div class="ver-mais-wrapper">
                    <h5><a href="" class="fw-medium text-decoration-underline" style="color: #0E810E">Ver mais</a></h5>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-md-12 col-10 mt-3">
                <div class="featured-carousel owl-carousel">';

    // Loop through services for this category
    foreach ($servicos as $servico) {
        $nome = htmlspecialchars($servico['nome']);
        $capa = htmlspecialchars($servico['capa']);
        echo '
                    <div class="item">
                        <div class="blog-entry">
                            <a href="#" class="block-20 d-flex align-items-start" style="background-image: url(\'images/' . $capa . '\');">
                            </a>
                            <div class="text border border-top-0 p-4 card-content">
                                <h3 class="heading"><a href="#">' . $nome . '</a></h3>
                                <div class="d-flex align-items-center mt-4">
                                    <p class="mb-0"><a href="#" class="btn btn-owl">Vê os profissionais <span class="ion-ios-arrow-round-forward"></span></a></p>
                                </div>
                            </div>
                        </div>
                    </div>';
    }

    echo '
                </div>
            </div>
        </div>
    </div>
</section>';
}

// Close connection
$conn = null;
?>

<script src='js/jquery.min.js'></script>
<script src='js/owl.carousel.min.js'></script>
<script src='js/owl.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('search');
        var dropdownItems = document.querySelectorAll('#dropdown-menu .dropdown-item');
        var dropdownMenu = document.getElementById('dropdown-menu');

        searchInput.addEventListener('input', function() {
            var input = this.value.trim().toLowerCase();

            dropdownItems.forEach(function(item) {
                var text = item.textContent.trim().toLowerCase();
                if (text.startsWith(input)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            var hasMatchingItems = Array.from(dropdownItems).some(function(item) {
                return item.style.display !== 'none';
            });

            if (hasMatchingItems && input !== '') {
                dropdownMenu.style.display = 'block';
            } else {
                dropdownMenu.style.display = 'none';
            }
        });

        dropdownItems.forEach(function(item) {
            item.addEventListener('click', function() {
                searchInput.value = this.textContent.trim();
                dropdownMenu.style.display = 'none';
            });
        });

        $('.featured-carousel').owlCarousel({
            loop: true,
            autoplay: true,
            margin: 30,
            nav: true,
            dots: true,
            items: 1,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        });
    });
</script>