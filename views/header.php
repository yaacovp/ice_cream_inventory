<!-- views/header.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock de Glaces</title>
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Inclusion de Chart.js -->
</head>
<body>
    <header>
        <h1>Gestion de Stock de Glaces</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="index.php?page=ice_creams">Gestion des Glaces</a></li>
                <li><a href="index.php?page=orders">Gestion des Commandes</a></li>
                <li><a href="index.php?page=synthesis">Synthèse des Glaces à Préparer</a></li>
            </ul>
        </nav>
    </header>
    <main>
