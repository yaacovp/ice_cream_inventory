<?php
// controllers/ice_creams.php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Logique pour gérer les glaces : afficher, ajouter, modifier, supprimer
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

switch ($action) {
    case 'add':
        include 'views/add_ice_cream.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Code pour ajouter la glace en base de données
            $flavor_id = $_POST['flavor_id'];
            $size = $_POST['size'];
            $stock = $_POST['stock'];
            
            $stmt = $pdo->prepare("INSERT INTO ice_creams (flavor_id, size, stock) VALUES (?, ?, ?)");
            $stmt->execute([$flavor_id, $size, $stock]);
            
            header('Location: index.php?page=ice_creams');
            exit();
        }
        break;
        
    case 'list':
    default:
        // Afficher la liste des glaces
        $stmt = $pdo->query("SELECT * FROM ice_creams");
        $ice_creams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/ice_creams.php';
        break;
}
?>