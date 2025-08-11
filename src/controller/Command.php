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

    public function update(int $id, string $name, string $email, string $phone_number)
    {
        $id = intval($id);
        $name = trim(htmlspecialchars($name));
        $email = trim(htmlspecialchars($email));
        $phone_number = trim(htmlspecialchars($phone_number));
        $mng = new ContactManager($this->pdo);
        $contact = $mng->find($id);
        if(!$contact) {
            echo "Ce contact n'existe pas en BDD.", PHP_EOL;
            return;
        }
        $mng->save($id, $name, $email, $phone_number);
        if(!$contact) {
            echo "Erreur lors de la mise à jour du contact.", PHP_EOL;
            return;
        }
        echo "Contact mis à jour.", PHP_EOL;
    }

    public function delete(int $id)
    {
        $id = intval($id);
        $mng = new ContactManager($this->pdo);
        $contact = $mng->find($id);
        if(!$contact) {
            echo "Ce contact n'existe pas en BDD.", PHP_EOL;
            return;
        }
        try {
            $mng->delete($id);
        } catch(\Exception $e) {
            echo "Erreur lors de la suppression du contact : ", $e->getMessage(), PHP_EOL;
            return;
        }
        $contact = $mng->find($id);
        if($contact) {
            echo "Erreur inconnue lors de la suppression du contact. Le contact est toujours présent en BDD.", PHP_EOL;
            return;
        }
        echo "Contact supprimé.", PHP_EOL;
    }

}