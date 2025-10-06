class CryptoDashboard {
    constructor() {
        this.trackedCryptos = [];
        this.allCryptos = [];
        this.chart = null;
        this.init();
    }

    async init() {
        await this.loadAllCryptos();
        await this.loadTrackedCryptos();
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    async loadAllCryptos() {
        try {
            const response = await fetch('/api/cryptos');
            const data = await response.json();
            
            if (data.status === 'success') {
                this.allCryptos = data.data;
            }
        } catch (error) {
            console.error('Error loading cryptos:', error);
        }
    }

    async loadTrackedCryptos() {
        try { 
            const response = await fetch('/api/tracked');
            const data = await response.json();
            
            console.log('Datos recibidos:', data);
            
            if (data.status === 'success') {
                this.trackedCryptos = data.data;
                console.log('Cryptos trac', this.trackedCryptos);
                this.renderTrackedCryptos();
                this.updateChart();
                this.updateSummary(); 
            } else {
                console.error('Error en respuesta:', data.message);
            }
        } catch (error) {
            console.error('Error loading tracked cryptos:', error);
        }
    }

    renderTrackedCryptos() {
        const container = document.getElementById('trackedCryptos');
         
        
        if (this.trackedCryptos.length === 0) {
            container.innerHTML = `
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
            `;
            return;
        }

        container.innerHTML = this.trackedCryptos.map(crypto => {
            const price = crypto.quote?.USD?.price || 0;
            const change = crypto.quote?.USD?.percent_change_24h || 0;
            const volume = crypto.quote?.USD?.volume_24h || 0;
            const symbol = crypto.symbol || crypto.name?.substring(0, 3).toUpperCase() || 'N/A';
            const marketCap = crypto.quote?.USD?.market_cap || 0;
            
            return `
                <div class="col-12 col-sm-6 col-lg-4 mb-3">
                    <div class="crypto-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 fw-bold">${crypto.name}</h6>
                                    <small class="text-muted">${symbol}</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger untrack-btn" 
                                        data-coin-id="${crypto.id}"
                                        title="Eliminar de seguimiento">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="crypto-price text-primary mb-2">
                                $${this.formatNumber(price)}
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="crypto-change ${change >= 0 ? 'positive' : 'negative'}">
                                    <i class="fas ${change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'} me-1"></i>
                                    ${change.toFixed(2)}%
                                </span>
                                <small class="text-muted">24h</small>
                            </div>
                            
                            <div class="volume-text text-muted">
                                Volumen: $${this.formatNumber(volume)}
                            </div>
                            ${marketCap ? `
                            <div class="volume-text text-muted">
                                Market Cap: $${this.formatNumber(marketCap)}
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
 
        container.querySelectorAll('.untrack-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const coinId = e.target.closest('.untrack-btn').dataset.coinId;
                this.untrackCrypto(coinId);
            });
        });
    }
 
    updateSummary() {
        const totalTracked = document.getElementById('totalTracked');
        const lastUpdate = document.getElementById('lastUpdate');
         
        totalTracked.textContent = this.trackedCryptos.length;
        
        // Actualizar última actualización
        const now = new Date();
        lastUpdate.textContent = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
         
        this.calculateAdditionalStats();
    }
 
    calculateAdditionalStats() {
        if (this.trackedCryptos.length === 0) return;
        
        const stats = {
            totalMarketCap: 0,
            averageChange: 0,
            topPerformer: null,
            worstPerformer: null
        };
        
        let totalChange = 0;
        let maxChange = -Infinity;
        let minChange = Infinity;
        
        this.trackedCryptos.forEach(crypto => {
            const change = crypto.quote?.USD?.percent_change_24h || 0;
            const marketCap = crypto.quote?.USD?.market_cap || 0;
            
            stats.totalMarketCap += marketCap;
            totalChange += change;
            
            if (change > maxChange) {
                maxChange = change;
                stats.topPerformer = crypto;
            }
            
            if (change < minChange) {
                minChange = change;
                stats.worstPerformer = crypto;
            }
        });
        
        stats.averageChange = totalChange / this.trackedCryptos.length;
        
        console.log('Estadísticas calculadas:', stats);
         
        this.displayAdvancedStats(stats);
    }
 
    displayAdvancedStats(stats) { 
        const statsContainer = document.getElementById('advancedStats');
        if (!statsContainer) return;
        
        statsContainer.innerHTML = `
            <div class="row text-center">
                <div class="col-6">
                    <small class="text-muted">Avg Change</small>
                    <p class="mb-0 ${stats.averageChange >= 0 ? 'positive' : 'negative'}">
                        ${stats.averageChange.toFixed(2)}%
                    </p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Top Performer</small>
                    <p class="mb-0 positive">
                        ${stats.topPerformer?.symbol || 'N/A'}
                    </p>
                </div>
            </div>
        `;
    }

    async trackCrypto(coinId, coinName) {
        try {
            this.showLoading();
            const numericCoinId = parseInt(coinId);
            
            const response = await fetch('/api/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    coin_id: numericCoinId,
                    coin_name: coinName
                })
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                await this.loadTrackedCryptos();
                this.hideModal('addCryptoModal');
                
                // Limpiar el campo de búsqueda
                document.getElementById('searchCrypto').value = '';
                document.getElementById('searchResults').innerHTML = '';
                 
                this.showNotification('Criptomoneda agregada exitosamente', 'success');
            } else {
                console.error('Error from server:', data.message);
                this.showNotification('Error al agregar criptomoneda', 'error');
            }
        } catch (error) {
            console.error('Error tracking crypto:', error);
            this.showNotification('Error de conexión', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async untrackCrypto(coinId) {
        try {
            const numericCoinId = parseInt(coinId);
            const response = await fetch(`/api/untrack/${numericCoinId}`, {
                method: 'DELETE'
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                await this.loadTrackedCryptos();
                this.showNotification('Criptomoneda eliminada', 'success');
            }
        } catch (error) {
            console.error('Error untracking crypto:', error);
            this.showNotification('Error al eliminar', 'error');
        }
    }

    setupEventListeners() { 
        const searchInput = document.getElementById('searchCrypto');
        searchInput.addEventListener('input', (e) => {
            this.searchCryptos(e.target.value);
        });
 
        const modal = document.getElementById('addCryptoModal');
        modal.addEventListener('hidden.bs.modal', () => {
            searchInput.value = '';
            document.getElementById('searchResults').innerHTML = '';
        });

        // Tecla Escape en búsqueda
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                document.getElementById('searchResults').innerHTML = '';
            }
        });
    }

    searchCryptos(query) {
        const resultsContainer = document.getElementById('searchResults');
        
        if (!query) {
            resultsContainer.innerHTML = '';
            return;
        }

        const filtered = this.allCryptos.filter(crypto => 
            crypto.name.toLowerCase().includes(query.toLowerCase()) ||
            crypto.symbol.toLowerCase().includes(query.toLowerCase())
        ).slice(0, 5);

        if (filtered.length === 0) {
            resultsContainer.innerHTML = '<div class="text-muted p-2 text-center">No se encontraron resultados</div>';
            return;
        }

        resultsContainer.innerHTML = filtered.map(crypto => {
            const price = crypto.quote?.USD?.price || 0;
            const change = crypto.quote?.USD?.percent_change_24h || 0;
            
            return `
                <div class="search-result-item p-3 rounded" 
                    data-coin-id="${crypto.id}"
                    data-coin-name="${crypto.name.replace(/"/g, '&quot;')}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${crypto.name}</strong> 
                            <small class="text-muted">(${crypto.symbol})</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">$${this.formatNumber(price)}</div>
                            <small class="${change >= 0 ? 'positive' : 'negative'}">
                                ${change.toFixed(2)}%
                            </small>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        this.addSearchResultListeners();
    }

    addSearchResultListeners() {
        const results = document.querySelectorAll('.search-result-item');
        results.forEach(item => {
            item.addEventListener('click', () => {
                const coinId = item.dataset.coinId;
                const coinName = item.dataset.coinName;
                this.trackCrypto(coinId, coinName);
            });
        });
    }

    updateChart() {
        const ctx = document.getElementById('priceChart').getContext('2d');
        
        if (this.chart) {
            this.chart.destroy();
        }

        if (this.trackedCryptos.length === 0) { 
            ctx.font = '14px Arial';
            ctx.fillStyle = '#adb5bd';
            ctx.textAlign = 'center';
            ctx.fillText('Agrega criptomonedas para ver el gráfico', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        const labels = this.trackedCryptos.map(crypto => crypto.symbol || crypto.name.substring(0, 4));
        const prices = this.trackedCryptos.map(crypto => crypto.quote?.USD?.price || 0);
        const colors = this.generateChartColors(prices.length);

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Precio Actual (USD)',
                    data: prices,
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('0.8', '1')),
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                return `Precio: $${this.formatNumber(context.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255,255,255,0.1)'
                        },
                        ticks: {
                            color: '#adb5bd'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#adb5bd'
                        }
                    }
                }
            }
        });
    }
 
    generateChartColors(count) {
        const colors = [
            'rgba(41, 98, 255, 0.8)',
            'rgba(0, 200, 83, 0.8)',
            'rgba(255, 23, 68, 0.8)',
            'rgba(255, 152, 0, 0.8)',
            'rgba(156, 39, 176, 0.8)',
            'rgba(0, 188, 212, 0.8)',
            'rgba(255, 193, 7, 0.8)',
            'rgba(76, 175, 80, 0.8)'
        ];
        
        return Array.from({ length: count }, (_, i) => colors[i % colors.length]);
    }
 
    showNotification(message, type = 'info') { 
        const toast = document.createElement('div');
        toast.className = `position-fixed top-0 end-0 p-3`;
        toast.style.zIndex = '9999';
        
        const bgColor = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        
        toast.innerHTML = `
            <div class="toast align-items-center text-white ${bgColor} border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto-remover después de 3 segundos
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }

    startAutoRefresh() { 
        setInterval(() => {
            this.showLoading();
            this.loadTrackedCryptos().finally(() => {
                this.hideLoading();
            });
        }, 30000);
    }

    showLoading() {
        document.getElementById('loading').style.display = 'block';
    }

    hideLoading() {
        document.getElementById('loading').style.display = 'none';
    }

    hideModal(modalId) {
        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modal) {
            modal.hide();
        }
    }

    formatNumber(num) {
        if (!num || isNaN(num)) return '0.00';
        
        if (num < 1) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 4,
                maximumFractionDigits: 6
            }).format(num);
        } else if (num < 1000) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        } else {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }
    }
}
 
document.addEventListener('DOMContentLoaded', function() {
    window.dashboard = new CryptoDashboard();
});