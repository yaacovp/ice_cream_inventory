<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $_POST['client_name'];
    
    // Vérifier que le nom du client n'est pas vide
    if (empty($client_name)) {
        die("Le nom du client est requis.");
    }
    
    try {
        // Démarrer une transaction
        $pdo->beginTransaction();
        
        // Ajouter le client dans la base de données
        $stmt = $pdo->prepare("INSERT INTO clients (name) VALUES (?)");
        $stmt->execute([$client_name]);
        $client_id = $pdo->lastInsertId();
        
        // Ajouter la commande dans la base de données
        $stmt = $pdo->prepare("INSERT INTO orders (client_id) VALUES (?)");
        $stmt->execute([$client_id]);
        $order_id = $pdo->lastInsertId();
        
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $flavor_id = $item['flavor_id'];
                $size = $item['size'];
                $quantity = $item['quantity'];
                
                if (!empty($flavor_id) && !empty($size) && !empty($quantity)) {
                    // Vérifier le stock et le stock réservé disponibles
                    $stmt = $pdo->prepare("
                        SELECT stock, reserved_stock
                        FROM ice_creams
                        WHERE flavor_id = ? AND size = ?
                    ");
                    $stmt->execute([$flavor_id, $size]);
                    $ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $reserved = 0;
                    $to_prepare = $quantity;
                    
                    if ($ice_cream) {
                        $available_stock = $ice_cream['stock'];
                        
                        if ($available_stock >= $quantity) {
                            // Réserver les glaces directement
                            $new_stock = $available_stock - $quantity;
                            $new_reserved = $ice_cream['reserved_stock'] + $quantity;
                            
                            $stmt = $pdo->prepare("
                                UPDATE ice_creams
                                SET stock = ?, reserved_stock = ?
                                WHERE flavor_id = ? AND size = ?
                            ");
                            $stmt->execute([$new_stock, $new_reserved, $flavor_id, $size]);
                            
                            $reserved = $quantity;
                            $to_prepare = 0;
                        } else {
                            // Réserver ce qui est disponible
                            $reserved = $available_stock;
                            $new_reserved = $ice_cream['reserved_stock'] + $reserved;
                            
                            $stmt = $pdo->prepare("
                                UPDATE ice_creams
                                SET stock = 0, reserved_stock = ?
                                WHERE flavor_id = ? AND size = ?
                            ");
                            $stmt->execute([$new_reserved, $flavor_id, $size]);
                            
                            // Calculer les quantités restantes à préparer
                            $to_prepare = $quantity - $reserved;
                        }
                    }
                    
                    if ($to_prepare > 0) {
                        // Vérifier si l'entrée existe déjà dans synthesis
                        $stmt = $pdo->prepare("SELECT quantity FROM synthesis WHERE flavor_id = ? AND size = ?");
                        $stmt->execute([$flavor_id, $size]);
                        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($existing) {
                            // Mettre à jour la quantité existante
                            $new_quantity = $existing['quantity'] + $to_prepare;
                            $stmt = $pdo->prepare("UPDATE synthesis SET quantity = ? WHERE flavor_id = ? AND size = ?");
                            $stmt->execute([$new_quantity, $flavor_id, $size]);
                        } else {
                            // Insérer une nouvelle entrée
                            $stmt = $pdo->prepare("INSERT INTO synthesis (flavor_id, size, quantity) VALUES (?, ?, ?)");
                            $stmt->execute([$flavor_id, $size, $to_prepare]);
                        }
                    }
                    
                    // Ajouter l'article à la commande
                    $stmt = $pdo->prepare("
                        INSERT INTO order_items (order_id, flavor_id, size, quantity)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$order_id, $flavor_id, $size, $quantity]);
                }
            }
        } else {
            throw new Exception("Aucun article n'a été ajouté à la commande.");
        }
        
        // Valider la transaction
        $pdo->commit();
        
        // Redirection vers la page des commandes après ajout
        header('Location: ../index.php?page=orders');
        exit();
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        die("Erreur lors de l'ajout de la commande : " . $e->getMessage());
    }
}
?>