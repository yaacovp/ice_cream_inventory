<!-- views/synthesis.php -->
<?php
// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';
?>

<h2>Synthèse des Glaces à Préparer</h2>

<?php
// Requête simple pour récupérer directement depuis la table synthesis
$query = "
    SELECT s.id, s.flavor_id, f.name AS flavor, s.size, s.quantity AS to_prepare
    FROM synthesis s
    JOIN flavors f ON s.flavor_id = f.id
    WHERE s.quantity > 0
    ORDER BY f.name, s.size
";
$stmt = $pdo->query($query);
$synthesis_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table>
    <tr>
        <th>Goût</th>
        <th>Litrage</th>
        <th>Quantité à Préparer (pots)</th>
        <th>Actions</th>
    </tr>
    <?php if (empty($synthesis_items)): ?>
        <tr>
            <td colspan="4" style="text-align: center; font-style: italic;">
                Aucune glace à préparer actuellement
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($synthesis_items as $item): ?>
        <tr>
            <td data-label="Goût"><?php echo htmlspecialchars($item['flavor']); ?></td>
            <td data-label="Litrage"><?php echo htmlspecialchars($item['size']); ?></td>
            <td data-label="Quantité"><?php echo htmlspecialchars($item['to_prepare']); ?></td>
            <td data-label="Actions">
                <button type="button" class="btn btn-success" 
                        onclick="handlePreparation('<?php echo $item['flavor_id']; ?>', '<?php echo htmlspecialchars($item['size']); ?>', <?php echo $item['to_prepare']; ?>, '<?php echo htmlspecialchars($item['flavor']); ?>')">
                    Fait
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

<p><em>Cette liste affiche les glaces qui doivent être préparées suite aux commandes passées.</em></p>

<!-- Formulaire caché pour l'envoi -->
<form id="preparationForm" action="controllers/prepare_ice_cream.php" method="POST" style="display: none;">
    <input type="hidden" name="flavor_id" id="form_flavor_id">
    <input type="hidden" name="size" id="form_size">
    <input type="hidden" name="quantity" id="form_quantity">
</form>

<script>
function handlePreparation(flavorId, size, totalQuantity, flavorName) {
    // Si il n'y a qu'une seule glace, directement traiter sans demander
    if (totalQuantity === 1) {
        submitPreparation(flavorId, size, totalQuantity);
        return;
    }
    
    // Si plusieurs glaces, demander si tout a été fait
    showYesNoDialog(
        `Avez-vous terminé TOUTES les ${totalQuantity} glaces ${flavorName} (${size}) ?`,
        function() {
            // Réponse "Oui" - tout a été fait
            submitPreparation(flavorId, size, totalQuantity);
        },
        function() {
            // Réponse "Non" - demander la quantité partielle
            askPartialQuantity(flavorId, size, totalQuantity, flavorName);
        }
    );
}

function askPartialQuantity(flavorId, size, totalQuantity, flavorName) {
    let quantityDone;
    do {
        const input = prompt(`Combien de glaces ${flavorName} (${size}) avez-vous terminées ?\n(Maximum: ${totalQuantity})`);
        
        if (input === null) return;
        
        quantityDone = parseInt(input);
        
        if (isNaN(quantityDone) || quantityDone < 1 || quantityDone > totalQuantity) {
            alert(`Veuillez entrer un nombre valide entre 1 et ${totalQuantity}`);
            quantityDone = null;
        }
    } while (quantityDone === null);
    
    submitPreparation(flavorId, size, quantityDone);
}

function showYesNoDialog(message, onYes, onNo) {
    // Créer l'overlay sombre
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999;
        display: flex; align-items: center; justify-content: center;
    `;
    
    // Créer la boîte de dialogue
    const dialog = document.createElement('div');
    dialog.style.cssText = `
        background: white; border-radius: 10px; padding: 20px; max-width: 90%; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.3); text-align: center;
    `;
    
    dialog.innerHTML = `
        <div style="font-size: 16px; margin-bottom: 20px; line-height: 1.4; color: #333;">
            ${message}
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="closeDialog(false)" style="
                padding: 12px 24px; border: 2px solid #e74c3c; background: white; 
                color: #e74c3c; border-radius: 6px; font-weight: bold; min-width: 80px;
            ">Non</button>
            <button onclick="closeDialog(true)" style="
                padding: 12px 24px; border: none; background: #27ae60; 
                color: white; border-radius: 6px; font-weight: bold; min-width: 80px;
            ">Oui</button>
        </div>
    `;
    
    overlay.appendChild(dialog);
    document.body.appendChild(overlay);
    
    // Fonction pour fermer la boîte
    window.closeDialog = function(isYes) {
        document.body.removeChild(overlay);
        if (isYes) {
            onYes();
        } else {
            onNo();
        }
        delete window.closeDialog; // Nettoyer la fonction globale
    };
}

function submitPreparation(flavorId, size, quantity) {
    document.getElementById('form_flavor_id').value = flavorId;
    document.getElementById('form_size').value = size;
    document.getElementById('form_quantity').value = quantity;
    document.getElementById('preparationForm').submit();
}
</script>