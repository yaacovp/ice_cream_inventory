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
<a href="index.php?page=add_ice_cream">Ajouter une glace</a>
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
            <!-- Modifier -->
            <a href="index.php?page=edit_ice_cream&id=<?= $ice_cream['id'] ?>">Modifier</a>
            <!-- Supprimer -->
            <a href="controllers/delete_ice_cream.php?id=<?= $ice_cream['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette glace ?');">Supprimer</a>
            </td>
    </tr>
    <?php endforeach; ?>
</table>

