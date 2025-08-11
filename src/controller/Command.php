<?php

namespace controller;

use infra\ContactManager;

class Command
{
    public function __construct(private \PDO $pdo)
    {

    }

    public function list(): void
    {
        $mng = new ContactManager();
        $allContacts = $mng->findAll($this->pdo);
        foreach ($allContacts as $contact) {
            echo "Contact : " . $contact . "\n";
        }
    }

}