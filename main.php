<?php
declare(strict_types=1);

while (true) {
    $line = readline("Entrez votre commande : ");
    if( strcasecmp($line,"list") === 0) {
        echo "Affichage de la liste : \n";
    } else {
        echo "Vous avez saisi : $line\n";
    }
}
