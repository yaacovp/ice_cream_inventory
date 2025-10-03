<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flavor_id = $_POST['flavor_id'];
    $size = $_POST['size'];
    $quantity = intval($_POST['quantity']); // S'assurer que c'est un entier
    
    // Vérifier que les données sont présentes et valides
    if (empty($flavor_id) || empty($size) || $quantity <= 0) {
        header('Location: ../index.php?page=synthesis&error=' . urlencode('Données manquantes ou invalides'));
        exit();
    }
    
    try {
        // Démarrer une transaction
        $pdo->beginTransaction();
        
        // Récupérer le nom du goût pour le message de succès
        $stmt = $pdo->prepare("SELECT name FROM flavors WHERE id = ?");
        $stmt->execute([$flavor_id]);
        $flavor_name = $stmt->fetchColumn();
        
        // Vérifier si le goût et le litrage existent déjà dans ice_creams
        $stmt = $pdo->prepare("SELECT reserved_stock FROM ice_creams WHERE flavor_id = ? AND size = ?");
        $stmt->execute([$flavor_id, $size]);
        $ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($ice_cream) {
            // Mettre à jour le stock réservé en ajoutant la quantité préparée
            $new_reserved = $ice_cream['reserved_stock'] + $quantity;
            
            $stmt = $pdo->prepare("UPDATE ice_creams SET reserved_stock = ? WHERE flavor_id = ? AND size = ?");
            $stmt->execute([$new_reserved, $flavor_id, $size]);
        } else {
            // Ajouter une nouvelle entrée avec le stock réservé si le goût et le litrage n'existent pas
            $stmt = $pdo->prepare("INSERT INTO ice_creams (flavor_id, size, stock, reserved_stock) VALUES (?, ?, 0, ?)");
            $stmt->execute([$flavor_id, $size, $quantity]);
        }
        
        // Vérifier la quantité restante dans synthesis
        $stmt = $pdo->prepare("SELECT quantity FROM synthesis WHERE flavor_id = ? AND size = ?");
        $stmt->execute([$flavor_id, $size]);
        $synthesis_entry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $message_type = '';
        if ($synthesis_entry) {
            $remaining_quantity = $synthesis_entry['quantity'] - $quantity;
            
            if ($remaining_quantity <= 0) {
                // Supprimer complètement l'entrée si tout est préparé
                $stmt = $pdo->prepare("DELETE FROM synthesis WHERE flavor_id = ? AND size = ?");
                $stmt->execute([$flavor_id, $size]);
                $message_type = 'complete';
            } else {
                // Réduire la quantité restante à préparer
                $stmt = $pdo->prepare("UPDATE synthesis SET quantity = ? WHERE flavor_id = ? AND size = ?");
                $stmt->execute([$remaining_quantity, $flavor_id, $size]);
                $message_type = 'partial';
            }
        }
        
        // Valider la transaction
        $pdo->commit();
        
        // Créer le message de succès
        if ($message_type === 'complete') {
            $success_message = "✅ {$quantity} glace(s) {$flavor_name} ({$size}) terminée(s) et ajoutée(s) au stock réservé !";
        } else {
            $success_message = "✅ {$quantity} glace(s) {$flavor_name} ({$size}) terminée(s). Il en reste encore à préparer.";
        }
        
        // Redirection vers la synthèse avec message de succès
        header('Location: ../index.php?page=synthesis&success=' . urlencode($success_message));
        exit();
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        
        // Redirection avec message d'erreur
        header('Location: ../index.php?page=synthesis&error=' . urlencode('Erreur lors de la préparation : ' . $e->getMessage()));
        exit();
    }
} else {
    // Redirection si accès direct sans POST
    header('Location: ../index.php?page=synthesis');
    exit();
}
?>