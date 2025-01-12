<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $_POST['client_name'];

    // Vérifier que le nom du client n'est pas vide
    if (empty($client_name)) {
        die("Le nom du client est requis.");
    }

    // Ajouter le client dans la base de données
    $stmt = $pdo->prepare("INSERT INTO clients (name) VALUES (?)");
    $stmt->execute([$client_name]);
    $client_id = $pdo->lastInsertId();

    // Ajouter la commande dans la base de données
    $stmt = $pdo->prepare("INSERT INTO orders (client_id) VALUES (?)");
    $stmt->execute([$client_id]);
    $order_id = $pdo->lastInsertId();

    if (!empty($_POST['items'])) {
        foreach ($_POST['items'] as $item) {
            $flavor_id = $item['flavor_id'];
            $size = $item['size'];
            $quantity = $item['quantity'];

            if (!empty($flavor_id) && !empty($size) && !empty($quantity)) {
                // Vérifier le stock et le stock réservé disponibles
                $stmt = $pdo->prepare("
                    SELECT stock, reserved_stock 
                    FROM ice_creams 
                    WHERE flavor_id = ? AND size = ?
                ");
                $stmt->execute([$flavor_id, $size]);
                $ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);

                $reserved = 0;
                $to_prepare = $quantity;

                if ($ice_cream) {
                    $available_stock = $ice_cream['stock'];

                    if ($available_stock >= $quantity) {
                        // Réserver les glaces directement
                        $stmt = $pdo->prepare("
                            UPDATE ice_creams 
                            SET stock = stock - ?, reserved_stock = reserved_stock + ? 
                            WHERE flavor_id = ? AND size = ?");
                        $stmt->execute([$quantity, $quantity, $flavor_id, $size]);
                        $reserved = $quantity;
                        $to_prepare = 0;
                    } else {
                        // Réserver ce qui est disponible
                        $reserved = $available_stock;

                        $stmt = $pdo->prepare("
                            UPDATE ice_creams 
                            SET stock = 0, reserved_stock = reserved_stock + ? 
                            WHERE flavor_id = ? AND size = ?");
                        $stmt->execute([$reserved, $flavor_id, $size]);

                        // Calculer les quantités restantes à préparer
                        $to_prepare = $quantity - $reserved;
                    }
                }

                if ($to_prepare > 0) {
                    // Ajouter les quantités manquantes à la table synthesis
                    $stmt = $pdo->prepare("
                        INSERT INTO synthesis (flavor_id, size, quantity) 
                        VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE quantity = quantity + ?");
                    $stmt->execute([$flavor_id, $size, $to_prepare, $to_prepare]);
                }

                // Ajouter l'article à la commande
                $stmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, flavor_id, size, quantity) 
                    VALUES (?, ?, ?, ?)");
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
