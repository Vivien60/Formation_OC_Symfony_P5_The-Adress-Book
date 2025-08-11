<?php
declare(strict_types=1);

require_once "autoload.php";
use \config\Conf;
use \infra\DBConnect;

$conf = Conf::fromInstance();
$pdo = DBConnect::fromInstance($conf->_config['bddConfig'])->getPDO();

while (true) {
    $line = readline("Entrez votre commande : ");
    $input = preg_match("/^(.+?)\s*((?<=\s)(.*))?$/", $line, $matches);
    $command = $matches[1];
    $args = $matches[2]??null;
    switch ($command) {
        case "list" :
            $commandController = new \controller\Command($pdo);
            $commandController->list();
            break;
        case "detail" :
            $commandController = new \controller\Command($pdo);
            $commandController->detail($args);
            break;
            break;
        case "create" :
            //create Spider Man, sm@marvel.com, 020202020
            if(empty($args)) {
                echo "Error", PHP_EOL, "Usage : create <name>,<email>,<phone_number>", PHP_EOL;
                break;
            }
            list($name, $email, $phone_number) = explode(",", $args);
            $commandController = new \controller\Command($pdo);
            $commandController->create($name, $email, $phone_number);
            break;
        default:
            echo "Vous avez saisi : $line \n";
    };
    unset($commandController);
}
