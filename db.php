<?php
// Connexion à la base de données
$host = 'localhost';
$db   = 'ice_cream_inventory';
$user = 'root';  // Remplacez si nécessaire
$pass = '';      // Remplacez si nécessaire

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    //echo "Connexion réussie"; // Ligne de test pour voir si la connexion fonctionne
} catch (PDOException $e) {
    //echo 'Erreur de connexion : ' . $e->getMessage();
    exit();
}

?>
