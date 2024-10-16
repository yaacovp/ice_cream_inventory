<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID de la commande
    if (isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];

        // Supprimer les articles associés à la commande dans order_items
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // Supprimer la commande elle-même
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);

        // Redirection après suppression
        header('Location: ../index.php?page=orders');
        exit();
    } else {
        echo "ID de la commande non spécifié.";
    }
} else {
    echo "Requête non valide.";
}
?>
