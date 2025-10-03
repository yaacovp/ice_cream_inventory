<div class="low-stocks">
    <h3>Récapitulatif : Stocks Faibles</h3>
    <?php if (!empty($low_stocks)): ?>
        <table>
            <tr>
                <th>Goût</th>
                <th>Taille</th>
                <th>Stock</th>
            </tr>
            <?php foreach ($low_stocks as $stock): ?>
                <tr style="color: <?php echo ($stock['stock'] == 1) ? 'red' : 'orange'; ?>;">
                    <td><?php echo htmlspecialchars($stock['flavor']); ?></td>
                    <td><?php echo htmlspecialchars($stock['size']); ?></td>
                    <td><?php echo htmlspecialchars($stock['stock']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucune glace avec un stock faible actuellement.</p>
    <?php endif; ?>
    <!-- Bouton pour accéder à l'ajout de stock -->
    <form action="index.php" method="GET">
        <input type="hidden" name="page" value="add_ice_cream">
        <button type="submit" class="btn btn-add" style="margin-top: 20px;">Ajouter du Stock</button>
    </form>
</div>