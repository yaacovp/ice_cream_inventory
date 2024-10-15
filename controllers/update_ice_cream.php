<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $ice_cream_id = $_POST['id'];
    $flavor_id = $_POST['flavor_id'];
    $size = $_POST['size'];
    $stock = $_POST['stock'];

    // Mettre à jour les informations de la glace
    $stmt = $pdo->prepare("UPDATE ice_creams SET flavor_id = ?, size = ?, stock = ? WHERE id = ?");
    $stmt->execute([$flavor_id, $size, $stock, $ice_cream_id]);

    // Redirection après la mise à jour
    header('Location: ../index.php?page=ice_creams');
    exit();
}
?>
