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

    /**
     * Instanciate Contact object with data from a database record
     * COmmand, not Query
     */
    private function contactFromRecord($record): ?Contact
    {
        if(empty($record) || empty($record["id"])) {
            return null;
        }

        /*
         * Keep your code shy
         * Tell, don't ask
         * https://www2.ccs.neu.edu/research/demeter/related-work/pragmatic-programmer/jan_03_enbug.pdf
         *
         * Ask :
        $contact = new Contact($record["id"]);
        $contact->setName($record["name"]);;
        $contact->setEmail($record["email"]);;
        $contact->setPhoneNumber($record["phone_number"]);
         * Tell :
         */
        return Contact::fromArray($record);
    }

    /**
     * Insert a new contact in a database
     */
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

    public function delete(int $id) : void
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

    /**
     * Update a contact in a database
     */
    public function save(int $id, string $name, string $email, string $phone_number) : void
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
}