<?php
declare(strict_types=1);

require_once "autoload.php";
use \config\Conf;
use \infra\DBConnect;

$conf = Conf::fromInstance();
$pdo = DBConnect::fromInstance($conf->_config['bddConfig'])->getPDO();

while (true) {
    $line = readline("Entrez votre commande : ");
    sscanf($line, "%s %s", $command, $args);;

    $commandController = new \controller\Command($pdo);
    switch ($command) {
        case "list" :
            $commandController->list();
            break;
        case "detail" :
            $commandController->detail($args);
            break;
        default:
            echo "Vous avez saisi : $line \n";
    };
}
