<!-- views/order_details.php -->
<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Récupérer les informations de la commande
$order_id = $_GET['order_id'];

// Récupérer les informations de la commande et du client
$stmt = $pdo->prepare("SELECT o.*, c.name FROM orders o JOIN clients c ON o.client_id = c.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si la commande existe
if (!$order) {
    echo "<p>Commande introuvable.</p>";
    exit();
}

// Récupérer les articles de la commande
$stmt = $pdo->prepare("
    SELECT oi.quantity, oi.size, f.name AS flavor_name
    FROM order_items oi
    JOIN flavors f ON oi.flavor_id = f.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour convertir le statut en classe CSS
function getStatusClass($status) {
    $status_lower = strtolower(str_replace(['é', 'è', ' '], ['e', 'e', '-'], $status));
    return $status_lower;
}

// Fonction pour formater la date
function formatDateDetailed($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d/m/Y à H:i');
}

$statusClass = getStatusClass($order['status']);
$formattedDate = formatDateDetailed($order['created_at']);
?>

<div class="order-details-container">
    <div class="order-details-card">
        <!-- Header avec titre et statut -->
        <div class="order-details-header">
            <h2 class="order-details-title">Détails de la Commande #<?php echo htmlspecialchars($order['id']); ?></h2>
            <span class="order-status-badge <?php echo $statusClass; ?>">
                <?php echo htmlspecialchars($order['status']); ?>
            </span>
        </div>
        
        <!-- Corps des détails -->
        <div class="order-details-body">
            <!-- Section informations générales -->
            <div class="order-info-section">
                <div class="order-info-grid">
                    <div class="info-card">
                        <div class="info-card-label">Client</div>
                        <div class="info-card-value"><?php echo htmlspecialchars($order['name']); ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-label">Date de création</div>
                        <div class="info-card-value"><?php echo $formattedDate; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Section articles -->
            <h3 class="section-title">Articles de la Commande</h3>
            <div class="articles-table-container">
                <table class="articles-table">
                    <thead>
                        <tr>
                            <th>Goût</th>
                            <th>Litrage</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td data-label="Goût"><?php echo htmlspecialchars($item['flavor_name']); ?></td>
                            <td data-label="Litrage"><?php echo htmlspecialchars($item['size']); ?></td>
                            <td data-label="Quantité">
                                <span class="quantity-badge"><?php echo htmlspecialchars($item['quantity']); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Bouton de retour -->
        <div class="back-button-container">
            <form action="index.php" method="GET" style="display:inline; width: 100%;">
                <input type="hidden" name="page" value="orders">
                <button type="submit" class="back-button">Retour à la liste des commandes</button>
            </form>
        </div>
    </div>
</div>