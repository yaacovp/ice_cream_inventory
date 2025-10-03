<?php
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    try {
        // Démarrer une transaction
        $pdo->beginTransaction();
        
        if ($status == 'Terminée') {
            // Récupérer les articles de la commande
            $stmt = $pdo->prepare("SELECT flavor_id, size, quantity FROM order_items WHERE order_id = ?");
            $stmt->execute([$order_id]);
            $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Traiter chaque article - Libérer le stock réservé
            foreach ($order_items as $item) {
                // Réduire le stock réservé (la commande est récupérée, plus besoin de réserver)
                $stmt = $pdo->prepare("
                    UPDATE ice_creams
                    SET reserved_stock = reserved_stock - ?
                    WHERE flavor_id = ? AND size = ? AND reserved_stock >= ?
                ");
                $stmt->execute([$item['quantity'], $item['flavor_id'], $item['size'], $item['quantity']]);
            }
            
            // Supprimer les articles de la commande
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$order_id]);
            
            // Supprimer la commande
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
        } else {
            // Si ce n'est pas "Terminée", juste mettre à jour le statut
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);
        }
        
        // Valider la transaction
        $pdo->commit();
        
        header('Location: ../index.php?page=orders');
        exit();
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "Erreur lors de la mise à jour du statut : " . $e->getMessage();
        echo "<br><a href='../index.php?page=orders'>Retour aux commandes</a>";
    }
}
?>