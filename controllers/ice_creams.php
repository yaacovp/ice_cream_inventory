<?php
// controllers/ice_creams.php

// Logique pour gérer les glaces : afficher, ajouter, modifier, supprimer

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

switch ($action) {
    case 'add':
        include 'views/add_ice_cream.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Code pour ajouter la glace en base de données
            $flavor = $_POST['flavor'];
            $size = $_POST['size'];
            $stock = $_POST['stock'];

            $stmt = $pdo->prepare("INSERT INTO ice_creams (flavor, size, stock) VALUES (?, ?, ?)");
            $stmt->execute([$flavor, $size, $stock]);

            header('Location: index.php?page=ice_creams');
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
