<?php
declare(strict_types=1);
namespace infra;

use domain\Contact;

class ContactManager
{
    public function findAll(\PDO $pdo): array
    {
        $contactStatement = $pdo->query("SELECT * FROM contact");
        return array_map([$this, "contactFromRecord"], $contactStatement->fetchAll());
    }

    private function contactFromRecord($record)
    {
        $contact = new Contact($record["id"]);
        $contact->setName($record["name"]);;
        $contact->setEmail($record["email"]);;
        $contact->setPhoneNumber($record["phone_number"]);;
        return $contact;
    }
}