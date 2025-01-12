<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Récupérer les glaces en stock
$stmt = $pdo->query("
    SELECT ice_creams.id, ice_creams.size, ice_creams.stock, flavors.name AS flavor
    FROM ice_creams
    JOIN flavors ON ice_creams.flavor_id = flavors.id
    WHERE ice_creams.stock > 0
");
$ice_creams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les glaces réservées
$stmt = $pdo->query("
    SELECT ice_creams.id, ice_creams.size, ice_creams.reserved_stock, flavors.name AS flavor
    FROM ice_creams
    JOIN flavors ON ice_creams.flavor_id = flavors.id
    WHERE ice_creams.reserved_stock > 0
");
$reserved_ice_creams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gestion des Glaces</h2>

<!-- Bouton pour ajouter une glace -->
<form action="index.php" method="GET" style="display:inline;">
    <input type="hidden" name="page" value="add_ice_cream">
    <button type="submit" class="btn btn-add" style="margin-bottom:10px;">Ajouter une glace</button>
</form>

<!-- Tableau des glaces en stock -->
<h3>Glaces en Stock</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Goût</th>
        <th>Taille</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($ice_creams as $ice_cream): ?>
    <tr>
        <td data-label="ID"><?= htmlspecialchars($ice_cream['id']) ?></td>
        <td data-label="Goût"><?= htmlspecialchars($ice_cream['flavor']) ?></td>
        <td data-label="Taille"><?= htmlspecialchars($ice_cream['size']) ?></td>
        <td data-label="Stock"><?= htmlspecialchars($ice_cream['stock']) ?></td>
        <td data-label="Actions">
            <!-- Modifier -->
            <form action="index.php" method="GET" style="display:inline;">
                <input type="hidden" name="page" value="edit_ice_cream">
                <input type="hidden" name="id" value="<?= htmlspecialchars($ice_cream['id']) ?>">
                <button type="submit" class="btn btn-edit">Modifier</button>
            </form>

            <!-- Supprimer -->
            <form action="controllers/delete_ice_cream.php" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette glace ?');">
                <input type="hidden" name="id" value="<?= htmlspecialchars($ice_cream['id']) ?>">
                <button type="submit" class="btn btn-delete">Supprimer</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Tableau des glaces réservées -->
<h3>Glaces Réservées</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Goût</th>
        <th>Taille</th>
        <th>Stock Réservé</th>
    </tr>
    <?php foreach ($reserved_ice_creams as $reserved): ?>
    <tr>
        <td data-label="ID"><?= htmlspecialchars($reserved['id']) ?></td>
        <td data-label="Goût"><?= htmlspecialchars($reserved['flavor']) ?></td>
        <td data-label="Taille"><?= htmlspecialchars($reserved['size']) ?></td>
        <td data-label="Stock Réservé"><?= htmlspecialchars($reserved['reserved_stock']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
