<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les informations de la commande
    $client_name = $_POST['client_name'];
    
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
        
        // Ajouter les articles de la commande
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $ice_cream_id = $item['ice_cream_id'];
                $quantity = $item['quantity'];
                
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, ice_cream_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$order_id, $ice_cream_id, $quantity]);
            }
        }
        
        // Valider la transaction
        $pdo->commit();
        
        // Redirection après traitement
        header('Location: ../index.php?page=orders');
        exit();
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        die("Erreur lors de l'ajout de la commande : " . $e->getMessage());
    }
}
?>