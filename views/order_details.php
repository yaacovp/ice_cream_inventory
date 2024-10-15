<!-- views/order_details.php -->

<?php
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

?>

<h2>Détails de la Commande #<?= htmlspecialchars($order['id']) ?></h2>
<p><strong>Client :</strong> <?= htmlspecialchars($order['name']) ?></p>
<p><strong>Date :</strong> <?= htmlspecialchars($order['created_at']) ?></p>
<p><strong>Statut :</strong> <?= htmlspecialchars($order['status']) ?></p>

<h3>Articles de la Commande</h3>
<table>
    <tr>
        <th>Goût</th>
        <th>Litrage</th>
        <th>Quantité</th>
    </tr>
    <?php foreach ($order_items as $item): ?>
    <tr>
        <td><?= htmlspecialchars($item['flavor_name']) ?></td>  <!-- Utiliser 'flavor_name' pour le goût -->
        <td><?= htmlspecialchars($item['size']) ?></td>         <!-- Utiliser 'size' pour le litrage -->
        <td><?= htmlspecialchars($item['quantity']) ?></td>
    </tr>
<?php endforeach; ?>

</table>

<a href="index.php?page=orders">Retour à la liste des commandes</a>
