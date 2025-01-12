<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flavor_id = $_POST['flavor_id'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];

    // Vérifier que les données sont présentes et valides
    if (empty($flavor_id) || empty($size) || empty($quantity)) {
        echo "Données manquantes ou invalides.<br>";
        exit();
    }

    // Vérifier si le goût et le litrage existent déjà dans ice_creams
    $stmt = $pdo->prepare("SELECT reserved_stock FROM ice_creams WHERE flavor_id = ? AND size = ?");
    $stmt->execute([$flavor_id, $size]);
    $ice_cream = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ice_cream) {
        // Mettre à jour le stock réservé en ajoutant la quantité à préparer
        $stmt = $pdo->prepare("UPDATE ice_creams SET reserved_stock = reserved_stock + ? WHERE flavor_id = ? AND size = ?");
        $stmt->execute([$quantity, $flavor_id, $size]);
    } else {
        // Ajouter une nouvelle entrée avec le stock réservé si le goût et le litrage n'existent pas
        $stmt = $pdo->prepare("INSERT INTO ice_creams (flavor_id, size, stock, reserved_stock) VALUES (?, ?, 0, ?)");
        $stmt->execute([$flavor_id, $size, $quantity]);
    }

    // Redirection vers la synthèse après la mise à jour du stock réservé
    header('Location: ../index.php?page=synthesis');
    exit();
}
?>
