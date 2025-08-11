<?php
declare(strict_types=1);

require_once "autoload.php";
use \config\Conf;
use \infra\DBConnect;

$conf = Conf::fromInstance();
$pdo = DBConnect::fromInstance($conf->_config['bddConfig'])->getPDO();

while (true) {
    $line = readline("Entrez votre commande : ");
    if( strcasecmp($line,"list") === 0) {
        echo "Affichage de la liste : \n";
        $command = new \controller\Command($pdo);
        $command->list();
    } else {
        echo "Vous avez saisi : $line\n";
    }
}
