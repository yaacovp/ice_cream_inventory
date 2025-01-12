<h2>Bienvenue dans l'application de gestion de stock de glaces !</h2>

<!-- Conteneur pour les graphiques -->
<div class="graph-container">
    <!-- Graphique des stocks -->
    <div>
        <h3>Aperçu du Stock</h3>
        <canvas id="stockChart"></canvas>
    </div>

    <!-- Graphique des commandes à préparer -->
    <div>
        <h3>Glaces à Préparer</h3>
        <canvas id="prepareChart"></canvas>
    </div>
</div>

<!-- Boutons d'actions principales -->
<h3>Actions Rapides</h3>
<form action="index.php" method="GET" style="display:inline;">
    <input type="hidden" name="page" value="add_ice_cream">
    <button type="submit" class="btn btn-add">Ajouter une nouvelle glace</button>
</form>

<form action="index.php" method="GET" style="display:inline;">
    <input type="hidden" name="page" value="add_order">
    <button type="submit" class="btn btn-add">Ajouter une nouvelle commande</button>
</form>

<!-- Script pour les graphiques -->
<script>
    // Données pour l'aperçu du stock
    const stockData = {
        labels: <?= json_encode(array_column($stock_summary, 'flavor')) ?>,
        datasets: [{
            label: 'Stock de Glaces',
            data: <?= json_encode(array_column($stock_summary, 'stock')) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    // Configuration du graphique de stock
    const stockConfig = {
        type: 'bar',
        data: stockData,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Initialiser le graphique de stock
    const stockChart = new Chart(
        document.getElementById('stockChart'),
        stockConfig
    );

    // Données pour l'aperçu des glaces à préparer
    const prepareData = {
        labels: <?= json_encode(array_column($tasks_to_prepare, 'flavor')) ?>,
        datasets: [{
            label: 'Glaces à Préparer',
            data: <?= json_encode(array_column($tasks_to_prepare, 'total_quantity')) ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    };

    // Configuration du graphique de préparation
    const prepareConfig = {
        type: 'bar',
        data: prepareData,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Initialiser le graphique de préparation
    const prepareChart = new Chart(
        document.getElementById('prepareChart'),
        prepareConfig
    );
</script>
