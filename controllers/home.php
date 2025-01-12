<?php
// controllers/home.php
include __DIR__ . '/../db.php';

// Récupérer le résumé du stock
$stmt = $pdo->query("
    SELECT f.name AS flavor, ic.size, ic.stock
    FROM ice_creams ic
    JOIN flavors f ON ic.flavor_id = f.id
");
$stock_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les commandes en cours
$stmt = $pdo->query("
    SELECT o.id, c.name AS client_name, o.created_at
    FROM orders o
    JOIN clients c ON o.client_id = c.id
    WHERE o.status = 'En préparation'
    ORDER BY o.created_at DESC
");
$current_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la synthèse des glaces à préparer
$stmt = $pdo->query("
    SELECT f.name AS flavor, oi.size, SUM(oi.quantity) AS total_quantity, COALESCE(ic.stock, 0) AS stock_available
    FROM order_items oi
    JOIN flavors f ON oi.flavor_id = f.id
    LEFT JOIN ice_creams ic ON ic.flavor_id = f.id AND ic.size = oi.size
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'En préparation'
    GROUP BY f.name, oi.size, ic.stock
");
$tasks_to_prepare = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
