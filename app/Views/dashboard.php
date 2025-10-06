<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoInvestment - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2962ff;
            --secondary-color: #00c853;
            --danger-color: #ff1744;
            --dark-bg: #1a1a2e;
            --card-bg: #2d3047;
            --text-light: #f8f9fa;
            --text-muted: #adb5bd;
        }

        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            color: var(--text-light);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(26, 26, 46, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .dashboard-card {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-color: var(--primary-color);
        }

        .crypto-card {
            background: linear-gradient(145deg, #2d3047, #25283d);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .crypto-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .crypto-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }

        .positive {
            color: var(--secondary-color);
            font-weight: 600;
        }

        .negative {
            color: var(--danger-color);
            font-weight: 600;
        }

        .chart-container {
            position: relative;
            height: 280px;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #304ffe);
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #304ffe, var(--primary-color));
            transform: translateY(-1px);
        }

        .btn-outline-danger {
            border-radius: 6px;
            padding: 0.25rem 0.5rem;
        }

        .search-result-item {
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .search-result-item:hover {
            background: rgba(41, 98, 255, 0.1);
            border-color: var(--primary-color);
            cursor: pointer;
        }

        .modal-content {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .form-control {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: var(--text-light);
            border-radius: 8px;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.15);
            border-color: var(--primary-color);
            color: var(--text-light);
            box-shadow: 0 0 0 0.2rem rgba(41, 98, 255, 0.25);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .crypto-price {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .crypto-change {
            font-size: 0.9rem;
        }

        .volume-text {
            font-size: 0.8rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .chart-container {
                height: 250px;
            }
            
            .crypto-card {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .dashboard-card .card-header h5 {
                font-size: 1.1rem;
            }
            
            .chart-container {
                height: 200px;
            }
        }

        /* Loading animation */
        .loading-spinner {
            border: 3px solid rgba(255,255,255,0.1);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body> 
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <span class="navbar-brand mb-0 h1 fw-bold">
                <i class="fas fa-chart-line me-2"></i>CryptoInvestment
            </span>
            <div class="navbar-text">
                <small class="text-muted">Tiempo Real</small>
            </div>
        </div>
    </nav>
 
    <div class="container py-4">
        <div class="row g-4"> 
            <div class="col-lg-8">
                <div class="dashboard-card">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Criptomonedas en Seguimiento
                            </h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCryptoModal">
                                <i class="fas fa-plus me-1"></i>Agregar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="trackedCryptos" class="row g-3"> 
                            <div class="col-12">
                                <div class="empty-state">
                                    <i class="fas fa-coins"></i>
                                    <h5 class="text-muted mb-3">No hay criptomonedas en seguimiento</h5>
                                    <p class="text-muted mb-3">Comienza agregando tus primeras criptomonedas para seguir su rendimiento</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCryptoModal">
                                        <i class="fas fa-plus me-1"></i>Agregar Primera Crypto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="col-lg-4"> 
                <div class="dashboard-card mb-4">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Comparación de Precios
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="priceChart"></canvas>
                        </div>
                    </div>
                </div>
 
                <div class="dashboard-card">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Resumen
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 id="totalTracked" class="text-primary mb-1">0</h4>
                                <small class="text-muted">Total Seguidas</small>
                            </div>
                            <div class="col-6">
                                <h4 id="lastUpdate" class="text-success mb-1">-</h4>
                                <small class="text-muted">Última Actualización</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div id="loading" class="text-center py-4" style="display: none;">
        <div class="loading-spinner"></div>
        <p class="mt-3 text-muted">Actualizando datos en tiempo real...</p>
    </div>
 
    <div class="modal fade" id="addCryptoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-search me-2"></i>Buscar Criptomoneda
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre o Símbolo</label>
                        <input type="text" class="form-control" id="searchCrypto" 
                               placeholder="Ej: Bitcoin, ETH, Cardano..." autocomplete="off">
                    </div>
                    <div id="searchResults" class="mt-3"> 
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>