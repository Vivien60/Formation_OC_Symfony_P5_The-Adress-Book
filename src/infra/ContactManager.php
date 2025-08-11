<?php
declare(strict_types=1);
namespace infra;

use domain\Contact;

class ContactManager
{

    public function __construct(private \PDO $pdo)
    {

    }

    /**
     * @return array<Contact>
     */
    public function findAll(): array
    {
        $contactStatement = $this->pdo->query("SELECT * FROM contact");
        return array_map([$this, "contactFromRecord"], $contactStatement->fetchAll());
    }

    public function find(int $id) : ?Contact
    {
        $contactStatement = $this->pdo->prepare("SELECT * FROM contact where id = :id");
        $contactStatement->execute([
            "id" => $id
        ]);
        $record = $contactStatement->fetch();
        return $this->contactFromRecord($record);
    }

    private function contactFromRecord($record): ?Contact
    {
        if(empty($record) || empty($record["id"])) {
            return null;
        }
        $contact = new Contact($record["id"]);
        $contact->setName($record["name"]);;
        $contact->setEmail($record["email"]);;
        $contact->setPhoneNumber($record["phone_number"]);;
        return $contact;
    }
}