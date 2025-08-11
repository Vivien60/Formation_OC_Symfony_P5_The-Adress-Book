<?php
declare(strict_types=1);

namespace controller;

use domain\Contact;
use infra\ContactManager;

class Command
{
    public function __construct(private \PDO $pdo)
    {

    }

    public function list(): void
    {
        echo "Affichage de la liste : \n";
        $mng = new ContactManager($this->pdo);
        $allContacts = $mng->findAll();
        foreach ($allContacts as $contact) {
            echo "Contact : ", $contact, PHP_EOL;
        }
    }

    public function detail($args)
    {
        $id = intval($args[0]);
        if($id < 1) {
            echo "ID invalide", PHP_EOL;
            return;
        }
        $mng = new ContactManager($this->pdo);
        $contact = $mng->find($id);
        if(!$contact) {
            echo "ID inconnu", PHP_EOL;
            return;
        }
        echo "Affichage du contact : ", PHP_EOL, $contact, PHP_EOL;
    }

}