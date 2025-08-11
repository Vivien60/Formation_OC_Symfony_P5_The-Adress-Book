<?php
declare(strict_types=1);
namespace infra;

use domain\Contact;
use exception\ReadContactException;

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
        try {
            $contactStatement = $this->pdo->prepare("SELECT * FROM contact where id = :id");
            $contactStatement->execute([
                "id" => $id
            ]);
            $record = $contactStatement->fetch();
        } catch(\Exception $e) {
            throw new ReadContactException($e);
        }
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
            throw new \exception\InsertContactException($e);
        }
    }

    public function delete(int $id)
    {
        $sql = "DELETE FROM contact WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                "id" => $id
            ]);
        } catch(\Exception $e) {
            throw new \exception\DeleteContactException($e);
        }
    }

    public function save(int $id, string $name, string $email, string $phone_number)
    {
        $sql = "UPDATE contact SET name = :name, email = :email, phone_number = :phone_number WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                "id" => $id,
                "name" => $name,
                "email" => $email,
                "phone_number" => $phone_number,
            ]);
        } catch(\Exception $e) {
            throw new \exception\UpdateContactException($e);
        }
    }

    public function fromInput(int $id, string $name, string $email, string $phone_number) : Contact
    {
        $contact = new Contact($id);
        $contact->setName($name);
        $contact->setEmail($email);
        $contact->setPhoneNumber($phone_number);
        return $contact;
    }
}