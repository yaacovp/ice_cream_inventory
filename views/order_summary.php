<!-- views/order_summary.php -->

<h2>Résumé des Commandes</h2>
<?php
// Récupérer toutes les commandes avec leurs articles
$stmt = $pdo->query("
    SELECT o.id AS order_id, c.name AS client_name, o.status, o.created_at,
           ic.flavor, ic.size, oi.quantity
    FROM orders o
    JOIN clients c ON o.client_id = c.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN ice_creams ic ON oi.ice_cream_id = ic.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les commandes
$orders_summary = [];
foreach ($orders as $order) {
    $orders_summary[$order['order_id']]['client_name'] = $order['client_name'];
    $orders_summary[$order['order_id']]['status'] = $order['status'];
    $orders_summary[$order['order_id']]['created_at'] = $order['created_at'];
    $orders_summary[$order['order_id']]['items'][] = [
        'flavor' => $order['flavor'],
        'size' => $order['size'],
        'quantity' => $order['quantity']
    ];
}
?>

<?php foreach ($orders_summary as $order_id => $order): ?>
    <h3>Commande #<?= htmlspecialchars($order_id) ?></h3>
    <p><strong>Client :</strong> <?= htmlspecialchars($order['client_name']) ?></p>
    <p><strong>Date :</strong> <?= htmlspecialchars($order['created_at']) ?></p>
    <p><strong>Statut :</strong> <?= htmlspecialchars($order['status']) ?></p>

    <table>
        <tr>
            <th>Goût</th>
            <th>Litrage</th>
            <th>Quantité</th>
        </tr>
        <?php foreach ($order['items'] as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['flavor']) ?></td>
            <td><?= htmlspecialchars($item['size']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endforeach; ?>

<a href="index.php">Retour à l'accueil</a>
