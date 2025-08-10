<?php
declare(strict_types=1);

require_once "autoload.php";
use \config\Conf;
use \infra\DBConnect;
use infra\ContactManager;

$conf = Conf::fromInstance();
$pdo = DBConnect::fromInstance($conf->_config['bddConfig'])->getPDO();;

while (true) {
    $line = readline("Entrez votre commande : ");
    if( strcasecmp($line,"list") === 0) {
        echo "Affichage de la liste : \n";
        $mng = new ContactManager();
        $allContacts = $mng->findAll($pdo);
        var_dump($allContacts);
    } else {
        echo "Vous avez saisi : $line\n";
    }
}
