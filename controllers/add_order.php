<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer le nom du client
    $client_name = $_POST['client_name'];
    
    // Vérifier que le nom du client n'est pas vide
    if (empty($client_name)) {
        die("Le nom du client est requis.");
    }

    // Ajouter le client dans la base de données
    $stmt = $pdo->prepare("INSERT INTO clients (name) VALUES (?)");
    $stmt->execute([$client_name]);
    $client_id = $pdo->lastInsertId();  // Récupérer l'ID du client ajouté

    // Ajouter la commande dans la base de données
    $stmt = $pdo->prepare("INSERT INTO orders (client_id) VALUES (?)");
    $stmt->execute([$client_id]);
    $order_id = $pdo->lastInsertId();  // Récupérer l'ID de la commande ajoutée

    // Vérifier que des articles ont bien été soumis
    if (!empty($_POST['items'])) {
        // Ajouter les articles de la commande
        foreach ($_POST['items'] as $item) {
            $flavor_id = $item['flavor_id'];  // On récupère l'ID du goût
            $size = $item['size'];  // Nouvelle colonne pour la taille
            $quantity = $item['quantity'];

            // Vérifier que l'ID du goût, la taille et la quantité sont valides
            if (!empty($flavor_id) && !empty($size) && !empty($quantity)) {
                // Insérer l'article dans la commande avec la taille
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, flavor_id, size, quantity) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $flavor_id, $size, $quantity]);
            }
        }
    } else {
        die("Aucun article n'a été ajouté à la commande.");
    }

    // Redirection vers la page des commandes après ajout
    header('Location: ../index.php?page=orders');
    exit();
}
?>
