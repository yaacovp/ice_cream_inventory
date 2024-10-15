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
        // Mettre à jour le stock des glaces en fonction des articles de commande
        $stmt = $pdo->prepare("SELECT flavor_id, quantity FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $stmt = $pdo->prepare("UPDATE ice_creams SET stock = stock - ? WHERE flavor_id = ?");
            $stmt->execute([$item['quantity'], $item['flavor_id']]);
        }
    }

    // Redirection après traitement
    header('Location: ../index.php?page=orders');
    exit();
}
?>
