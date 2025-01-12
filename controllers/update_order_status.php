<?php
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);

    if ($status == 'Terminée') {
        $stmt = $pdo->prepare("SELECT flavor_id, size, quantity FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $stmt = $pdo->prepare("
                UPDATE ice_creams 
                SET reserved_stock = reserved_stock - ? 
                WHERE flavor_id = ? AND size = ?");
            $stmt->execute([$item['quantity'], $item['flavor_id'], $item['size']]);
        }

        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);

        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
    }

    header('Location: ../index.php?page=orders');
    exit();
}
?>
