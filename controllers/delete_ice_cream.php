<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
       
        // Debugging : afficher l'ID reçu
        echo "ID reçu : " . htmlspecialchars($id) . "<br>";
        
        try {
            // Démarrer une transaction
            $pdo->beginTransaction();
            
            // Récupérer le flavor_id correspondant à cet id dans ice_creams
            $stmt = $pdo->prepare("SELECT flavor_id FROM ice_creams WHERE id = ?");
            $stmt->execute([$id]);
            $ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($ice_cream) {
                $flavor_id = $ice_cream['flavor_id'];
                
                // Supprimer les articles de commande liés dans order_items
                $stmt = $pdo->prepare("DELETE FROM order_items WHERE flavor_id = ?");
                $stmt->execute([$flavor_id]);
                
                // Supprimer la glace de la table ice_creams
                $stmt = $pdo->prepare("DELETE FROM ice_creams WHERE id = ?");
                $stmt->execute([$id]);
                
                // Valider la transaction
                $pdo->commit();
                
                // Redirection après suppression
                header('Location: ../index.php?page=ice_creams');
                exit();
            } else {
                $pdo->rollBack();
                echo "Aucun flavor_id correspondant à cet ID.";
            }
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            echo "Erreur lors de la suppression : " . $e->getMessage();
        }
    } else {
        echo "ID non spécifié.";
    }
} else {
    echo "Requête non valide.";
}
?>