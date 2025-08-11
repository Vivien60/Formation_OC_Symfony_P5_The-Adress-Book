<?php
declare(strict_types=1);
namespace infra;

use domain\Contact;

class InsertContactException extends \RuntimeException {}

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

    public function create(string $name, string $email, string $phone_number): int
    {
        $sql = "INSERT INTO contact (name, email, phone_number) VALUES (:name, :email, :phone_number)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                "name" => $name,
                "email" => $email,
                "phone_number" => $phone_number,
            ]);
            return intval($this->pdo->lastInsertId());
        } catch (\Exception $e) {
            throw new InsertContactException($e);
        }
    }
}