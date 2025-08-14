<?php
declare(strict_types=1);
error_reporting(E_ALL);

require_once "autoload.php";

use \config\Conf;
use \infra\DBConnect;

$conf = Conf::fromInstance();
$pdo = DBConnect::fromInstance($conf->_config['bddConfig'])->getPDO();



while (true) {
    $line = readline("Entrez votre commande (help, list, detail, create, update, delete, quit): ");
    if(empty($line)) {
        continue;
    }
    $line = trim($line);
    $parser = new \infra\CommandParser($line);
    $hasError = !$parser->build()->validate();
    $command = $parser->getCommand();
    $args = $parser->getArgs();
    switch ($command) {
        case "list" :
            $commandController = new \controller\Command($pdo);
            echo $commandController->list();
            break;
        case "detail" :
            if($hasError) {
                echo "Error", PHP_EOL, 'Usage: detail <id>', PHP_EOL;
                break;
            }
            $commandController = new \controller\Command($pdo);
            echo $commandController->detail($args[0]);
            break;
        case "create" :
            //create Spider Man, sm@marvel.com, 020202020
            $usage = "create <name>,<email>,<phone_number>";
            if($hasError) {
                echo "Error", PHP_EOL, 'Usage: create <name>,<email>,<phone_number>', PHP_EOL;
                break;
            }
            list($name, $email, $phone_number) = $args;
            $commandController = new \controller\Command($pdo);
            echo $commandController->create($name, $email, $phone_number);
            break;
        case "update" :
            //create Spider Man, sm@marvel.com, 020202020
            if($hasError) {
                echo "Error", PHP_EOL, "Usage : update <id>,<name>,<email>,<phone_number>", PHP_EOL;
                break;
            }
            list($id, $name, $email, $phone_number) = $args;
            $commandController = new \controller\Command($pdo);
            echo $commandController->update($id, $name, $email, $phone_number);
            break;
        case "delete" :
            //delete 8
            if($hasError) {
                echo "Error", PHP_EOL, 'Usage: delete <id>', PHP_EOL;
                break;
            }
            $id = $args[0];
            $commandController = new \controller\Command($pdo);
            echo $commandController->delete($id);
            break;
        case "quit" :
            echo "Bye bye !", PHP_EOL;
            exit();
        default:
            echo "Commande inconnue.", PHP_EOL, "Usage:", PHP_EOL;
        case "help" :
            echo "list : lister les contacts", PHP_EOL,
            "detail <id> : afficher un contact", PHP_EOL,
            "create <name>,<email>,<phone_number> : créer un contact", PHP_EOL,
            "delete <id> : supprimer un contact", PHP_EOL;
            break;
    }
    unset($commandController);
    unset($parser);
}
