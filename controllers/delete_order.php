<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID de la commande
    if (isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];
        
        try {
            // Démarrer une transaction
            $pdo->beginTransaction();
            
            // Récupérer les articles de la commande pour ajuster les stocks
            $stmt = $pdo->prepare("
                SELECT flavor_id, size, quantity
                FROM order_items
                WHERE order_id = ?
            ");
            $stmt->execute([$order_id]);
            $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Traiter chaque article de la commande
            foreach ($order_items as $item) {
                // Vérifier combien ont été réservés
                $stmt = $pdo->prepare("
                    SELECT reserved_stock
                    FROM ice_creams
                    WHERE flavor_id = ? AND size = ?
                ");
                $stmt->execute([$item['flavor_id'], $item['size']]);
                $ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($ice_cream) {
                    $reserved_stock = $ice_cream['reserved_stock'];
                    $to_return = min($item['quantity'], $reserved_stock);
                    
                    // Réintégrer uniquement le stock réservé
                    if ($to_return > 0) {
                        $stmt = $pdo->prepare("
                            UPDATE ice_creams
                            SET stock = stock + ?, reserved_stock = reserved_stock - ?
                            WHERE flavor_id = ? AND size = ?
                        ");
                        $stmt->execute([$to_return, $to_return, $item['flavor_id'], $item['size']]);
                    }
                    
                    // Ajuster les quantités dans la synthèse de glaces à préparer
                    $remaining_quantity = $item['quantity'] - $to_return;
                    if ($remaining_quantity > 0) {
                        // Vérifier si l'entrée existe dans synthesis
                        $stmt = $pdo->prepare("
                            SELECT quantity FROM synthesis 
                            WHERE flavor_id = ? AND size = ?
                        ");
                        $stmt->execute([$item['flavor_id'], $item['size']]);
                        $synthesis = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($synthesis) {
                            $new_quantity = $synthesis['quantity'] - $remaining_quantity;
                            
                            if ($new_quantity <= 0) {
                                // Supprimer l'entrée si la quantité devient 0 ou négative
                                $stmt = $pdo->prepare("
                                    DELETE FROM synthesis 
                                    WHERE flavor_id = ? AND size = ?
                                ");
                                $stmt->execute([$item['flavor_id'], $item['size']]);
                            } else {
                                // Mettre à jour la quantité
                                $stmt = $pdo->prepare("
                                    UPDATE synthesis 
                                    SET quantity = ?
                                    WHERE flavor_id = ? AND size = ?
                                ");
                                $stmt->execute([$new_quantity, $item['flavor_id'], $item['size']]);
                            }
                        }
                    }
                }
            }
            
            // Supprimer les articles associés à la commande dans order_items
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$order_id]);
            
            // Supprimer la commande elle-même
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
            
            // Nettoyer les entrées synthesis avec quantité <= 0
            $stmt = $pdo->prepare("DELETE FROM synthesis WHERE quantity <= 0");
            $stmt->execute();
            
            // Valider la transaction
            $pdo->commit();
            
            // Redirection après suppression
            header('Location: ../index.php?page=orders');
            exit();
            
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            echo "Erreur lors de la suppression de la commande : " . $e->getMessage();
        }
    } else {
        echo "ID de la commande non spécifié.";
    }
} else {
    echo "Requête non valide.";
}
?>