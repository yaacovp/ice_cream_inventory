<?php
echo "<h2>Test systématique de connexion</h2>";

// Combinaisons à tester
$configs = array(
    array("sql.free.fr", "perezyaacov", "Jp30087600..!!"),
    array("mysql.free.fr", "perezyaacov", "Jp30087600..!!"),
    array("sql.free.fr", "perezyaacov_sql", "Jp30087600..!!"),
    array("sql.free.fr", "perezyaacov", ""), // mot de passe vide
);

foreach ($configs as $i => $config) {
    list($host, $user, $pass) = $config;
    echo "<br>Test " . ($i+1) . ": $host / $user / " . ($pass ? "***" : "vide") . "<br>";
    
    $connection = @mysql_connect($host, $user, $pass);
    if ($connection) {
        echo "✅ CONNEXION REUSSIE !<br>";
        
        // Tester la sélection de base
        $bases = array("perezyaacov", $user, $user . "_db");
        foreach ($bases as $base) {
            $select = @mysql_select_db($base, $connection);
            if ($select) {
                echo "✅ Base '$base' sélectionnée !<br>";
                
                // Créer une table de test
                $sql = "CREATE TABLE IF NOT EXISTS test_table (id int)";
                if (mysql_query($sql)) {
                    echo "✅ Création de table réussie !<br>";
                    echo "<h3>PARAMETRES CORRECTS TROUVES :</h3>";
                    echo "Host: $host<br>";
                    echo "User: $user<br>";
                    echo "Pass: " . ($pass ? "***" : "vide") . "<br>";
                    echo "Base: $base<br>";
                    exit();
                }
                break;
            }
        }
        mysql_close($connection);
    }
}

echo "<br>❌ Aucune configuration ne fonctionne.";
?>