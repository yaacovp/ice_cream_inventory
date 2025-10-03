<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Requête pour récupérer les glaces avec un stock faible
$query = "
    SELECT f.name AS flavor, ic.size, ic.stock
    FROM ice_creams ic
    JOIN flavors f ON ic.flavor_id = f.id
    WHERE ic.stock > 0 AND ic.stock <= 2
    ORDER BY ic.stock ASC
";

$stmt = $pdo->query($query);
$low_stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>