<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];  // Récupérer l'ID de l'URL

    // Récupérer le flavor_id correspondant à cet id dans ice_creams
    $stmt = $pdo->prepare("SELECT flavor_id FROM ice_creams WHERE id = ?");
    $stmt->execute([$id]);
    $flavor_id = $stmt->fetchColumn();

    if ($flavor_id) {
        // Supprimer les articles de commande liés dans order_items
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE flavor_id = ?");
        if ($stmt->execute([$flavor_id])) {
            echo "Suppression réussie dans order_items.<br>";
        } else {
            echo "Erreur lors de la suppression dans order_items.<br>";
        }

        // Supprimer l'enregistrement dans ice_creams
        $stmt = $pdo->prepare("DELETE FROM ice_creams WHERE id = ?");
        if ($stmt->execute([$id])) {
            echo "Suppression réussie dans ice_creams.<br>";
        } else {
            echo "Erreur lors de la suppression dans ice_creams.<br>";
        }
    } else {
        echo "Aucun flavor_id correspondant à cet ID.<br>";
    }

    // Redirection après suppression
    header('Location: ../index.php?page=ice_creams');
    exit();
} else {
    echo "ID non spécifié.";
}
