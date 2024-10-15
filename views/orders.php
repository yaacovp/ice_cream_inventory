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
<a href="index.php?page=add_order">Ajouter une nouvelle commande</a>
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
            <a href="index.php?page=order_details&order_id=<?= $order['id'] ?>">Voir Détails</a>
            <form method="POST" action="/ice_cream_inventory/controllers/update_order_status.php" style="display:inline;">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <?php if ($order['status'] == 'En préparation'): ?>
                    <input type="hidden" name="status" value="Terminée">
                    <button type="submit">Marquer comme Terminée</button>
                <?php else: ?>
                    <button type="button" disabled>Terminée</button>
                <?php endif; ?>
            </form>
            <a href="controllers/delete_order.php?order_id=<?= $order['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
