<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Activer l'affichage et la journalisation des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flavor_id = $_POST['flavor_id'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];

    // Vérifier que les données sont présentes et valides
    if (empty($flavor_id) || empty($size) || empty($quantity)) {
        echo "Données manquantes ou invalides.<br>";
        exit();
    }

    // Afficher les informations pour débogage
    echo "flavor_id: " . htmlspecialchars($flavor_id) . "<br>";
    echo "size: " . htmlspecialchars($size) . "<br>";
    echo "quantity: " . htmlspecialchars($quantity) . "<br>";

    // Vérifier si le goût existe déjà dans ice_creams
    $stmt = $pdo->prepare("SELECT stock FROM ice_creams WHERE flavor_id = ? AND size = ?");
    $stmt->execute([$flavor_id, $size]);
    $ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ice_cream) {
        // Mettre à jour le stock en ajoutant la quantité à préparer
        $stmt = $pdo->prepare("UPDATE ice_creams SET stock = stock + ? WHERE flavor_id = ? AND size = ?");
        if ($stmt->execute([$quantity, $flavor_id, $size])) {
            echo "Stock mis à jour avec succès.<br>";
        } else {
            echo "Erreur lors de la mise à jour du stock.<br>";
            exit();
        }
    } else {
        // Si le goût n'existe pas dans ice_creams, ajouter une nouvelle entrée
        $stmt = $pdo->prepare("INSERT INTO ice_creams (flavor_id, size, stock) VALUES (?, ?, ?)");
        if ($stmt->execute([$flavor_id, $size, $quantity])) {
            echo "Nouveau goût ajouté et stock mis à jour.<br>";
        } else {
            echo "Erreur lors de l'ajout du nouveau goût.<br>";
            exit();
        }
    }

    // Redirection vers la synthèse après la mise à jour du stock
    header('Location: ../index.php?page=synthesis');
    exit();
}
?>
