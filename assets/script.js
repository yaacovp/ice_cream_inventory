// assets/script.js

let itemIndex = 1;

function addOrderItem() {
    const orderItems = document.getElementById('order-items');
    const newItem = document.createElement('div');
    newItem.className = 'order-item';

    // Créer les éléments du nouvel article
    const labelFlavor = document.createElement('label');
    labelFlavor.textContent = 'Goût :';

    const selectFlavor = document.createElement('select');
    selectFlavor.name = `items[${itemIndex}][ice_cream_id]`;
    selectFlavor.required = true;

    const labelQuantity = document.createElement('label');
    labelQuantity.textContent = 'Quantité :';

    const inputQuantity = document.createElement('input');
    inputQuantity.type = 'number';
    inputQuantity.name = `items[${itemIndex}][quantity]`;
    inputQuantity.min = 1;
    inputQuantity.required = true;

    // Ajouter les éléments au nouvel article
    newItem.appendChild(labelFlavor);
    newItem.appendChild(selectFlavor);
    newItem.appendChild(labelQuantity);
    newItem.appendChild(inputQuantity);

    // Ajouter le nouvel article au formulaire
    orderItems.appendChild(newItem);
    itemIndex++;
}
