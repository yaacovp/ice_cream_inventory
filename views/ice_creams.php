<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Récupérer la liste des glaces avec leur goût en joignant les tables ice_creams et flavors
$stmt = $pdo->query("
    SELECT ice_creams.id, ice_creams.size, ice_creams.stock, flavors.name AS flavor
    FROM ice_creams
    JOIN flavors ON ice_creams.flavor_id = flavors.id
");
$ice_creams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Afficher la liste des glaces -->
<h2>Liste des Glaces</h2>

<!-- Bouton pour ajouter une glace -->
<form action="index.php" method="GET" style="display:inline;">
    <input type="hidden" name="page" value="add_ice_cream">
    <button type="submit" class="btn btn-add" style="margin-bottom:10px;">Ajouter une glace</button>
</form>

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
        <td><?= htmlspecialchars($ice_cream['id']) ?></td>
        <td><?= htmlspecialchars($ice_cream['flavor']) ?></td>
        <td><?= htmlspecialchars($ice_cream['size']) ?></td>
        <td><?= htmlspecialchars($ice_cream['stock']) ?></td>
        <td>
            <!-- Bouton pour modifier -->
            <form action="index.php" method="GET" style="display:inline;">
                <input type="hidden" name="page" value="edit_ice_cream">
                <input type="hidden" name="id" value="<?= $ice_cream['id'] ?>">
                <button type="submit" class="btn btn-edit">Modifier</button>
            </form>

            <!-- Bouton pour supprimer -->
            <form action="controllers/delete_ice_cream.php" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette glace ?');">
    <input type="hidden" name="id" value="<?= htmlspecialchars($ice_cream['id']) ?>"> <!-- Sécurisation avec htmlspecialchars -->
    <button type="submit" class="btn btn-delete">Supprimer</button>
</form>

            
        </td>
    </tr>
    <?php endforeach; ?>
</table>
