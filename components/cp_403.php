

<head>
   <style>
        /* Error page specific styles only */
        .error-container {
            max-width: 800px;
            min-width: 45vw;
            max-height: 80vh;
            text-align: center;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 3rem 2rem;
            margin: 2rem;
        }

        .error-icon {
            font-size: 8rem;
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0rem;
            animation: pulse 2s infinite;
        }

        .error-code {
            font-size: 4rem;
            font-weight: 700;
            background: linear-gradient(to right, #00798F, #0E810E);
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="">
      

        <!-- Main Content -->
        <div class=" justify-content-center d-flex vh-75">
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="error-code">403</div>
                <h1 class="error-title">Acesso Negado</h1>
                <p class="error-message">
                    Desculpe, você não tem permissão para aceder a esta página.<br>
                    Se acredita que isto é um erro, por favor entre em <a href="./contacto.php">contacto</a>.
                </p>
                
                <div class="error-actions">
                    <a href="./index.php" class="btn btn-outline-azul px-4 py-2">
                        <i class="fas fa-home "></i> Voltar ao Início
                    </a>
                    <a href="./login.php" class="btn btn-outline-verde px-4 py-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                    </a>
                    <a href="./ajuda.php" class="btn btn-outline-secondary px-4 py-2">
                        <i class="fas fa-question-circle me-2"></i>Ajuda
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
