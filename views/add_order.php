<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Récupérer les goûts depuis la table flavors
$stmt = $pdo->query("SELECT * FROM flavors");
$flavors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" type="text/css" href="/asset/styles.css">
<!-- Formulaire pour ajouter une commande -->
<h2>Ajouter une Commande</h2>
<form method="POST" action="/ice_cream_inventory/controllers/add_order.php">
    <label>Nom du Client :</label>
    <input type="text" name="client_name" required>

    <div id="order-items">
    <h3>Articles de la Commande</h3>
    <div class="order-item">
        <label>Goût :</label>
        <select name="items[0][flavor_id]" required>
            <?php foreach ($flavors as $flavor): ?>
                <option value="<?= $flavor['id'] ?>">
                    <?= htmlspecialchars($flavor['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Taille :</label>
        <select name="items[0][size]" required>
            <option value="500ml">500ml</option>
            <option value="1L">1L</option>
        </select>

        <label>Quantité :</label>
        <input type="number" name="items[0][quantity]" min="1" required>
    </div>
</div>

<button type="button" onclick="addOrderItem()">Ajouter un article</button>
<button type="submit">Passer la commande</button>

<script>
let itemIndex = 1;

function addOrderItem() {
    const orderItems = document.getElementById('order-items');
    const newItem = document.createElement('div');
    newItem.className = 'order-item';
    
    // Clone de la liste des goûts pour permettre plusieurs articles
    const selectFlavor = document.querySelector("select[name='items[0][flavor_id]']").cloneNode(true);
    selectFlavor.name = `items[${itemIndex}][flavor_id]`;

    // Liste déroulante pour la taille
    const selectSize = document.createElement('select');
    selectSize.name = `items[${itemIndex}][size]`;
    selectSize.required = true;
    selectSize.innerHTML = `
        <option value="500ml">500ml</option>
        <option value="1L">1L</option>
    `;

    // Champ pour la quantité
    const inputQuantity = document.createElement('input');
    inputQuantity.type = 'number';
    inputQuantity.name = `items[${itemIndex}][quantity]`;
    inputQuantity.min = 1;
    inputQuantity.required = true;

    // Ajout des éléments dans la nouvelle ligne d'article
    newItem.appendChild(selectFlavor);
    newItem.appendChild(document.createTextNode(' Taille : '));
    newItem.appendChild(selectSize);
    newItem.appendChild(document.createTextNode(' Quantité : '));
    newItem.appendChild(inputQuantity);

    orderItems.appendChild(newItem);
    itemIndex++;
}
</script>

