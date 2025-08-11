<?php
declare(strict_types=1);

namespace controller;

use domain\Contact;
use infra\ContactManager;
use infra\InsertContactException;

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

    public function create(string $name, string $email, string $phone_number)
    {
        $name = trim(htmlspecialchars($name));
        $email = trim(htmlspecialchars($email));
        $phone_number = trim(htmlspecialchars($phone_number));
        $mng = new ContactManager($this->pdo);
        try {
            $contact = $mng->create($name, $email, $phone_number);
        } catch (InsertContactException $e) {
            echo "Erreur lors de l'insertion du contact : ", $e->getMessage(), PHP_EOL;
            return;
        }
        $contact = $mng->find($contact);
        echo "Contact créé : ", $contact, PHP_EOL;
    }

}