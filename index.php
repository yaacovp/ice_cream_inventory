<?php
// index.php
include 'db.php';
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

include 'views/header.php';

switch ($page) {
    case 'ice_creams':
        include 'views/ice_creams.php';
        break;
    case 'orders':
        include 'views/orders.php';
        break;
    case 'add_order':
        include 'views/add_order.php';
        break;
    case 'order_details':
        include 'views/order_details.php';
        break;
    case 'edit_ice_cream':
        include 'views/edit_ice_cream.php';
        break;
    case 'add_ice_cream':  // Ajoutez ce cas pour ajouter une glace
        include 'views/add_ice_cream.php';
        break;
    case 'synthesis':
        include 'views/synthesis.php';
        break;
    case 'home':  // Ajout de la page d'accueil
        include 'controllers/home.php';
        include 'views/home.php';
        break;
    default:
        echo '<h2>Bienvenue dans l\'application de gestion de stock de glaces !</h2>';
        break;
}

include 'views/footer.php';
?>
