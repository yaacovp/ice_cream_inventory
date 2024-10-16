<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        
        // Debugging : afficher l'ID reçu
        echo "ID reçu : " . htmlspecialchars($id) . "<br>";

        // Récupérer le flavor_id correspondant à cet id dans ice_creams
        $stmt = $pdo->prepare("SELECT flavor_id FROM ice_creams WHERE id = ?");
        $stmt->execute([$id]);
        $flavor_id = $stmt->fetchColumn();

        if ($flavor_id) {
            // Supprimer les articles de commande liés dans order_items
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE flavor_id = ?");
            $stmt->execute([$flavor_id]);

            // Supprimer la glace de la table ice_creams
            $stmt = $pdo->prepare("DELETE FROM ice_creams WHERE id = ?");
            $stmt->execute([$id]);

            // Redirection après suppression
            header('Location: ../index.php?page=ice_creams');
            exit();
        } else {
            echo "Aucun flavor_id correspondant à cet ID.";
        }
    } else {
        echo "ID non spécifié.";
    }
} else {
    echo "Requête non valide.";
}
?>
