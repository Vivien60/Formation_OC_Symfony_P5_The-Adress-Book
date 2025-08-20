<?php
declare(strict_types=1);
namespace model\contact;

class Contact implements \Stringable
{
    private int $id;
    private string $name;
    private string $email;
    private string $phone_number;

    /**
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
        $this->name = "<empty>";
        $this->email = "<empty>";
        $this->phone_number = "<empty>";
    }

    public static function fromArray($record)
    {
        $contact = new static($record["id"]);
        $contact->setName($record["name"]);
        $contact->setEmail($record["email"]);
        $contact->setPhoneNumber($record["phone_number"]);
        return $contact;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return trim($this->name);
    }

    public function getEmail(): string
    {
        return trim($this->email);
    }

    public function getPhoneNumber(): string
    {
        return trim($this->phone_number);
    }

    public function setName(string $name): void
    {
        $this->name = $name?:"<empty>";
    }

    public function setEmail(string $email): void
    {
        $this->email = $email?:"<empty>";
    }

    public function setPhoneNumber(string $phone_number): void
    {
        $this->phone_number = $phone_number?:"<empty>";
    }

    public function __toString() : string
    {
        return "$this->id : $this->name, $this->email, $this->phone_number";
    }
}