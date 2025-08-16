<?php
declare(strict_types=1);

namespace controller;

use exception\{ReadContactException, UpdateContactException, InsertContactException};
use infra\ContactManager;

class Command
{
    public function __construct(private \PDO $pdo)
    {

    }

    public function list(): string
    {
        $result = "Affichage de la liste : \n";
        $mng = new ContactManager($this->pdo);
        $allContacts = $mng->findAll();
        foreach ($allContacts as $contact) {
            $result .= "Contact : " . $contact . PHP_EOL;
        }
        return $result;
    }

    public function detail(int $id) : string
    {
        $id = intval($id);
        $mng = new ContactManager($this->pdo);
        try {
            $contact = $mng->find($id);
        } catch(ReadContactException $e) {
            return "Erreur lors de la récupération du contact." . PHP_EOL;
        } catch(\InvalidArgumentException $e) {
            return $e->getMessage() . PHP_EOL;
        }
        if(!$contact) {
            return "ID inconnu" . PHP_EOL;
        }
        return "Affichage du contact : " . PHP_EOL . $contact . PHP_EOL;
    }

    public function create(string $name, string $email='', string $phone_number='') : string
    {
        $name = trim(htmlspecialchars($name));
        $email = trim(htmlspecialchars($email));
        $phone_number = trim(htmlspecialchars($phone_number));
        $mng = new ContactManager($this->pdo);
        try {
            $contact = $mng->create($name, $email, $phone_number);
        } catch (InsertContactException $e) {
            return "Erreur lors de l'insertion du contact : " . PHP_EOL . $e->getMessage() . PHP_EOL;
        }
        try {
            $contact = $mng->find($contact);
        } catch(ReadContactException $e) {
            return "Erreur lors de la récupération du contact." . PHP_EOL;
        } catch(\InvalidArgumentException $e) {
            return $e->getMessage() . PHP_EOL;
        }
        return "Contact créé : " . $contact . PHP_EOL;
    }

    public function update(int $id, string $name='', string $email='', string $phone_number='') : string
    {
        $id = intval($id);
        $name = trim(htmlspecialchars($name));
        $email = trim(htmlspecialchars($email));
        $phone_number = trim(htmlspecialchars($phone_number));
        $mng = new ContactManager($this->pdo);
        $contact = $mng->find($id);
        if(!$contact) {
            return "Ce contact n'existe pas en BDD." . PHP_EOL;
        }
        try {
            $mng->save($id, $name, $email, $phone_number);
        } catch(UpdateContactException $e) {
            return "Erreur lors de la mise à jour du contact." . PHP_EOL . $e->getMessage() . PHP_EOL;
        }
        return "Contact mis à jour." . PHP_EOL;
    }

    public function delete(int $id) : string
    {
        $id = intval($id);
        if($id < 1) {
            return "ID invalide : L'id doit être un entier positif non null." . PHP_EOL;
        }
        $mng = new ContactManager($this->pdo);
        $contact = $mng->find($id);
        if(!$contact) {
            return "Ce contact n'existe pas en BDD." . PHP_EOL;
        }
        try {
            $mng->delete($id);
        } catch(\Exception $e) {
            return "Erreur lors de la suppression du contact : " . $e->getMessage() . PHP_EOL;
        }
        $contact = $mng->find($id);
        if($contact) {
            return "Erreur inconnue lors de la suppression du contact. Le contact est toujours présent en BDD." . PHP_EOL;
        }
        return "Contact supprimé." . PHP_EOL;
    }

}