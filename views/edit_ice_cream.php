<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Récupérer les informations de la glace
if (isset($_GET['id'])) {
    $ice_cream_id = $_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM ice_creams WHERE id = ?");
    $stmt->execute([$ice_cream_id]);
    $ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Récupérer les goûts pour la liste déroulante
    $stmt = $pdo->query("SELECT * FROM flavors");
    $flavors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("ID de la glace non spécifié.");
}
?>

<h2>Modifier la Glace</h2>
<form method="POST" action="controllers/update_ice_cream.php">
    <input type="hidden" name="id" value="<?= $ice_cream['id'] ?>">

    <label>Goût :</label>
    <select name="flavor_id" required>
        <?php foreach ($flavors as $flavor): ?>
            <option value="<?= $flavor['id'] ?>" <?= $ice_cream['flavor_id'] == $flavor['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($flavor['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Taille :</label>
    <select name="size" required>
        <option value="500ml" <?= $ice_cream['size'] == '500ml' ? 'selected' : '' ?>>500ml</option>
        <option value="1L" <?= $ice_cream['size'] == '1L' ? 'selected' : '' ?>>1L</option>
    </select>

    <label>Quantité (nombre de pots) :</label>
    <input type="number" name="stock" min="1" value="<?= $ice_cream['stock'] ?>" required>

    <button type="submit">Modifier la Glace</button>
</form>
