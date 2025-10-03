<?php
// Forcer l'encodage UTF-8 pour toute l'application
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

// Détection d'environnement
if ($_SERVER['HTTP_HOST'] == 'localhost:5000' || $_SERVER['HTTP_HOST'] == 'localhost') {
    // Version locale
    $host = 'localhost';
    $db   = 'ice_cream_inventory';
    $user = 'root';
    $pass = '';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]);
    } catch (PDOException $e) {
        echo 'Erreur de connexion : ' . $e->getMessage();
        exit();
    }
} else {
    // Version InfinityFree
    $host = "sql112.infinityfree.com";
    $dbname = "if0_40040724_ice_cream_inventory";
    $user = "if0_40040724";
    $pass = "6hCmFSJGZeFxWkG";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]);
    } catch (PDOException $e) {
        echo 'Erreur de connexion : ' . $e->getMessage();
        exit();
    }
}
?>