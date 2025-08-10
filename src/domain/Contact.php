<?php
declare(strict_types=1);
namespace domain;

class Contact
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
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPhoneNumber(string $phone_number): void
    {
        $this->phone_number = $phone_number;
    }

    public function __toString() : string
    {
        return "Contact : $this->name, $this->email, $this->phone_number";
    }
}