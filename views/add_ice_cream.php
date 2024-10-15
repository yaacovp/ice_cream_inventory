<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Récupérer les goûts depuis la table flavors
$stmt = $pdo->query("SELECT * FROM flavors");
$flavors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Formulaire pour ajouter une glace -->
<h2>Ajouter une Glace</h2>
<form method="POST" action="controllers/add_ice_cream.php">
    <label>Goût :</label>
    <select name="flavor_id" required>
        <?php foreach ($flavors as $flavor): ?>
            <option value="<?= $flavor['id'] ?>">
                <?= htmlspecialchars($flavor['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Taille :</label>
    <select name="size" required>
        <option value="500ml">500ml</option>
        <option value="1L">1L</option>
    </select>

    <label>Quantité (nombre de pots) :</label>
    <input type="number" name="stock" min="1" required>

    <button type="submit">Ajouter la Glace</button>
</form>
