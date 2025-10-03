<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Récupérer les commandes et les clients associés
$query = "
    SELECT o.id, c.name AS client_name, o.status, o.created_at
    FROM orders o
    JOIN clients c ON o.client_id = c.id
    ORDER BY o.created_at DESC
";
$stmt = $pdo->query($query);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour convertir le statut en classe CSS
function getStatusClass($status) {
    $status_lower = strtolower(str_replace(['é', 'è', ' '], ['e', 'e', '-'], $status));
    return $status_lower;
}

// Fonction pour formater la date
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return [
        'date' => $date->format('d/m/Y'),
        'time' => $date->format('H:i')
    ];
}
?>

<!-- Header avec bouton d'ajout -->
<h2>Liste des Commandes</h2>

<!-- Bouton pour ajouter une nouvelle commande -->
<form action="index.php" method="GET" style="display:inline;">
    <input type="hidden" name="page" value="add_order">
    <button type="submit" class="btn btn-add" style="margin-bottom:20px;">Ajouter une nouvelle commande</button>
</form>

<!-- Conteneur des cards -->
<div class="orders-container">
    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 40px; color: #6c757d;">
            <p>Aucune commande trouvée.</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            $statusClass = getStatusClass($order['status']);
            $dateFormatted = formatDate($order['created_at']);
        ?>
            <div class="order-card">
                <!-- Header avec ID et badge de statut -->
                <div class="order-header">
                    <h3 class="order-id">Commande #<?php echo htmlspecialchars($order['id']); ?></h3>
                    <span class="status-badge <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                </div>
                
                <!-- Corps de la card avec informations -->
                <div class="order-body">
                    <div class="order-info">
                        <div class="info-item">
                            <span class="info-label">Client</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['client_name']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date</span>
                            <span class="info-value order-date">
                                <?php echo $dateFormatted['date']; ?><br>
                                <?php echo $dateFormatted['time']; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Actions en bas de la card -->
                <div class="order-actions">
                    <!-- Bouton pour voir les détails -->
                    <form action="index.php" method="GET" style="display:inline;">
                        <input type="hidden" name="page" value="order_details">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="btn btn-details">Détails</button>
                    </form>
                    
                    <!-- Bouton pour terminer la commande -->
                    <?php if ($order['status'] != 'Terminée'): ?>
                        <form method="POST" action="controllers/update_order_status.php" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="status" value="Terminée">
                            <button type="submit" class="btn btn-terminate">Terminer</button>
                        </form>
                    <?php endif; ?>
                    
                    <!-- Bouton pour supprimer -->
                    <form action="controllers/delete_order.php" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">
                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                        <button type="submit" class="btn btn-delete">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>