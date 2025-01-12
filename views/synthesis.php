<!-- views/synthesis.php -->
<h2>Synthèse des Glaces à Préparer</h2>
<?php
// Requête pour récupérer les commandes en cours et le stock
$stmt = $pdo->query("
    SELECT f.id AS flavor_id, f.name AS flavor, oi.size, SUM(oi.quantity) AS total_quantity, 
           COALESCE(ic.stock, 0) AS stock_available, 
           COALESCE(ic.reserved_stock, 0) AS reserved_stock
    FROM order_items oi
    JOIN flavors f ON oi.flavor_id = f.id
    LEFT JOIN ice_creams ic ON ic.flavor_id = f.id AND ic.size = oi.size
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'En préparation'
    GROUP BY f.name, oi.size, ic.stock, ic.reserved_stock
");

$synthesis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table>
    <tr>
        <th>Goût</th>
        <th>Litrage</th>
        <th>Quantité à Préparer (pots)</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($synthesis as $item): ?>
        <?php
        // Calculer la quantité à préparer en fonction des commandes, du stock et des réservations
        $to_prepare = $item['total_quantity'] - ($item['stock_available'] + $item['reserved_stock']);
        if ($to_prepare > 0): // Afficher uniquement les glaces à préparer
        ?>
        <tr>
            <td><?= htmlspecialchars($item['flavor']) ?></td>
            <td><?= htmlspecialchars($item['size']) ?></td>
            <td><?= htmlspecialchars($to_prepare) ?></td>
            <td>
                <form action="/ice_cream_inventory/controllers/prepare_ice_cream.php" method="POST">
                    <input type="hidden" name="flavor_id" value="<?= htmlspecialchars($item['flavor_id']) ?>">
                    <input type="hidden" name="size" value="<?= htmlspecialchars($item['size']) ?>">
                    <input type="hidden" name="quantity" value="<?= htmlspecialchars($to_prepare) ?>">
                    <button type="submit" class="btn btn-success">Fait</button>
                </form>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
