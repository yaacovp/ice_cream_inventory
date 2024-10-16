<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Récupérer les commandes et les clients associés
$stmt = $pdo->query("
    SELECT o.id, c.name AS client_name, o.status, o.created_at 
    FROM orders o
    JOIN clients c ON o.client_id = c.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Afficher la liste des commandes -->
<h2>Liste des Commandes</h2>

<!-- Bouton pour ajouter une nouvelle commande -->
<form action="index.php" method="GET" style="display:inline;">
    <input type="hidden" name="page" value="add_order">
    <button type="submit" class="btn btn-add">Ajouter une nouvelle commande</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Client</th>
        <th>Statut</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($orders as $order): ?>
    <tr>
        <td><?= htmlspecialchars($order['id']) ?></td>
        <td><?= htmlspecialchars($order['client_name']) ?></td>
        <td><?= htmlspecialchars($order['status']) ?></td>
        <td><?= htmlspecialchars($order['created_at']) ?></td>
        <td>
            <!-- Bouton pour voir les détails -->
            <form action="index.php" method="GET" style="display:inline;">
                <input type="hidden" name="page" value="order_details">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button type="submit" class="btn btn-details">Voir Détails</button>
            </form>

            <!-- Bouton pour marquer comme terminée -->
            <form method="POST" action="/ice_cream_inventory/controllers/update_order_status.php" style="display:inline;">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <?php if ($order['status'] == 'En préparation'): ?>
                    <input type="hidden" name="status" value="Terminée">
                    <button type="submit" class="btn btn-terminate">Marquer comme Terminée</button>
                <?php else: ?>
                    <button type="button" class="btn btn-terminate" disabled>Terminée</button>
                <?php endif; ?>
            </form>

            <!-- Bouton pour supprimer -->
<form action="controllers/delete_order.php" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">
    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
    <button type="submit" class="btn btn-delete">Supprimer</button>
</form>

        </td>
    </tr>
    <?php endforeach; ?>
</table>
