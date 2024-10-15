<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ajouter un log pour vérifier si le formulaire est soumis
file_put_contents('log.txt', "Formulaire soumis.\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $flavor_id = $_POST['flavor_id'];
    $size = $_POST['size'];
    $stock = $_POST['stock'];

    // Ajouter un log pour les données du formulaire
    file_put_contents('log.txt', "Données reçues : flavor_id=$flavor_id, size=$size, stock=$stock\n", FILE_APPEND);

    // Vérifier si une glace avec le même goût et taille existe déjà
    $stmt = $pdo->prepare("SELECT id, stock FROM ice_creams WHERE flavor_id = ? AND size = ?");
    $stmt->execute([$flavor_id, $size]);
    $existing_ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_ice_cream) {
        // Si la glace existe déjà, on met à jour la quantité
        $new_stock = $existing_ice_cream['stock'] + $stock;
        $stmt = $pdo->prepare("UPDATE ice_creams SET stock = ? WHERE id = ?");
        $stmt->execute([$new_stock, $existing_ice_cream['id']]);
        
        // Log pour mise à jour
        file_put_contents('log.txt', "Mise à jour de la glace avec ID : {$existing_ice_cream['id']} à nouveau stock : $new_stock\n", FILE_APPEND);
    } else {
        // Si la glace n'existe pas, on l'ajoute
        $stmt = $pdo->prepare("INSERT INTO ice_creams (flavor_id, size, stock) VALUES (?, ?, ?)");
        $stmt->execute([$flavor_id, $size, $stock]);
        
        // Log pour ajout
        file_put_contents('log.txt', "Ajout d'une nouvelle glace : flavor_id=$flavor_id, size=$size, stock=$stock\n", FILE_APPEND);
    }

    // Redirection après l'ajout ou la mise à jour
    header('Location: ../index.php?page=ice_creams');
    exit();
}
?>
