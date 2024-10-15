<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if (isset($_GET['order_id'])) {
    // Récupérer l'ID de la commande
    $order_id = $_GET['order_id'];

    // Supprimer les articles de la commande dans la table order_items
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->execute([$order_id]);

    // Supprimer la commande dans la table orders
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);

    // Redirection vers la page des commandes après suppression
    header('Location: ../index.php?page=orders');
    exit();
} else {
    echo "ID de la commande non spécifié.";
}
?>
