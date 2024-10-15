<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les informations de la commande
    $client_name = $_POST['client_name'];

    // Ajouter le client dans la base de données
    $stmt = $pdo->prepare("INSERT INTO clients (name) VALUES (?)");
    $stmt->execute([$client_name]);
    $client_id = $pdo->lastInsertId();

    // Ajouter la commande dans la base de données
    $stmt = $pdo->prepare("INSERT INTO orders (client_id) VALUES (?)");
    $stmt->execute([$client_id]);
    $order_id = $pdo->lastInsertId();

    // Ajouter les articles de la commande
    foreach ($_POST['items'] as $item) {
        $ice_cream_id = $item['ice_cream_id'];
        $quantity = $item['quantity'];

        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, ice_cream_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$order_id, $ice_cream_id, $quantity]);
    }

    // Redirection après traitement
    header('Location: ../index.php?page=orders');
    exit();
}
?>
