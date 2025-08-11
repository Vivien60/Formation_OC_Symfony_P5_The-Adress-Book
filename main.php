<?php
declare(strict_types=1);

require_once "autoload.php";
use \config\Conf;
use \infra\DBConnect;

$conf = Conf::fromInstance();
$pdo = DBConnect::fromInstance($conf->_config['bddConfig'])->getPDO();

while (true) {
    $line = readline("Entrez votre commande : ");
    $hasError = false;
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
        case "update" :
            //create Spider Man, sm@marvel.com, 020202020
            if(empty($args)) {
                $hasError = true;
            } else {
                list($id, $name, $email, $phone_number) = explode(",", $args);
                $id = intval($id);
                if(empty($id)) {
                    $hasError = true;
                }
            }
            if($hasError) {
                echo "Error", PHP_EOL, "Usage : update <id>,<name>,<email>,<phone_number>", PHP_EOL;
                break;
            }
            $commandController = new \controller\Command($pdo);
            $commandController->update($id, $name, $email, $phone_number);
            break;
        case "delete" :
            //delete 8
            $id = intval($args);
            if(empty($id)) {
                echo "Error", PHP_EOL, "Usage : delete <id>", PHP_EOL;
                break;
            }
            $commandController = new \controller\Command($pdo);
            $commandController->delete($id);
            break;
        case "help" :
            echo "list : lister les contacts", PHP_EOL,
            "detail <id> : afficher un contact", PHP_EOL,
            "create <name>,<email>,<phone_number> : créer un contact", PHP_EOL,
            "delete <id> : supprimer un contact", PHP_EOL;
            break;
        default:
            echo "Vous avez saisi : $line \n";
    };
    unset($commandController);
}
