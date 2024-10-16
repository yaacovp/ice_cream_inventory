<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Mettre à jour le statut de la commande
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);

    if ($status == 'Terminée') {
        // Récupérer les articles de la commande
        $stmt = $pdo->prepare("SELECT flavor_id, size, quantity FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mettre à jour le stock des glaces pour chaque article de la commande
        foreach ($items as $item) {
            // Déduire la quantité commandée du stock de chaque glace
            $stmt = $pdo->prepare("UPDATE ice_creams SET stock = stock - ? WHERE flavor_id = ? AND size = ?");
            $stmt->execute([$item['quantity'], $item['flavor_id'], $item['size']]);
        }

        // Supprimer les articles de la commande
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // Supprimer la commande elle-même
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
    }

    // Redirection après traitement
    header('Location: ../index.php?page=orders');
    exit();
}
