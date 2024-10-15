<!-- views/synthesis.php -->
<h2>Synthèse des Glaces à Préparer</h2>
<?php
// Requête pour récupérer les commandes en cours et le stock
$stmt = $pdo->query("
    SELECT f.name AS flavor, oi.size, SUM(oi.quantity) AS total_quantity, 
           COALESCE(ic.stock, 0) AS stock_available
    FROM order_items oi
    JOIN flavors f ON oi.flavor_id = f.id
    LEFT JOIN ice_creams ic ON ic.flavor_id = f.id AND ic.size = oi.size
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'En préparation'
    GROUP BY f.name, oi.size, ic.stock
");

$synthesis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table>
    <tr>
        <th>Goût</th>
        <th>Litrage</th>
        <th>Quantité à Préparer (pots)</th>
    </tr>
    <?php foreach ($synthesis as $item): ?>
        <?php
        // Calculer la quantité à préparer en fonction des commandes et du stock
        $to_prepare = $item['total_quantity'] - $item['stock_available'];
        if ($to_prepare > 0): // Afficher uniquement les glaces à préparer
        ?>
        <tr>
            <td><?= htmlspecialchars($item['flavor']) ?></td>
            <td><?= htmlspecialchars($item['size']) ?></td>
            <td><?= htmlspecialchars($to_prepare) ?></td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
