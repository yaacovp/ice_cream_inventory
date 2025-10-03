<!-- views/order_summary.php -->
<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';
?>

<h2>Résumé des Commandes</h2>
<?php
// Récupérer toutes les commandes avec leurs articles
$query = "
    SELECT o.id AS order_id, c.name AS client_name, o.status, o.created_at,
           f.name AS flavor, oi.size, oi.quantity
    FROM orders o
    JOIN clients c ON o.client_id = c.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN flavors f ON oi.flavor_id = f.id
    ORDER BY o.created_at DESC
";
$stmt = $pdo->query($query);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les commandes
$orders_summary = array();
foreach ($orders as $order) {
    $orders_summary[$order['order_id']]['client_name'] = $order['client_name'];
    $orders_summary[$order['order_id']]['status'] = $order['status'];
    $orders_summary[$order['order_id']]['created_at'] = $order['created_at'];
    $orders_summary[$order['order_id']]['items'][] = array(
        'flavor' => $order['flavor'],
        'size' => $order['size'],
        'quantity' => $order['quantity']
    );
}
?>

<?php foreach ($orders_summary as $order_id => $order): ?>
    <h3>Commande #<?php echo htmlspecialchars($order_id); ?></h3>
    <p><strong>Client :</strong> <?php echo htmlspecialchars($order['client_name']); ?></p>
    <p><strong>Date :</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
    <p><strong>Statut :</strong> <?php echo htmlspecialchars($order['status']); ?></p>
    
    <table>
        <tr>
            <th>Goût</th>
            <th>Litrage</th>
            <th>Quantité</th>
        </tr>
        <?php foreach ($order['items'] as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['flavor']); ?></td>
            <td><?php echo htmlspecialchars($item['size']); ?></td>
            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endforeach; ?>

<a href="index.php">Retour à l'accueil</a>