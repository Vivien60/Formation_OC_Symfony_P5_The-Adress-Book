<?php
declare(strict_types=1);

namespace controller;

use exception\{InsertContactException, ReadContactException, UpdateContactException};
use model\contact\ContactManager;

/**
 * //TODO : pour respecter MVC : ne pas retourner un message directement,
 *      mais un json, avec une erreur, son code et son descriptif,
 *      en structurant de manière à ce que la vue (main.php, qui est aussi un routeur)
 *      ne soit pas nécessairement dépendante du message textuel, mais puisse comprendre techniquement l'erreur
 *      et afficher son propre message.
 *      Par ailleurs pour respecter MVC, il faudrait aussi une couche Model, enfin bon ^^
 *      Ensuite, s'amuser à le faire en DDD :
 *      src/
 *      ├── domain/
 *      │   ├── Contact.php (DTO)
 *      │   └── ContactRepositoryInterface.php
 *      ├── infrastructure/
 *      │   ├── DatabaseContactRepository.php (ex-ContactManager)
 *      │   └── DatabaseConnection.php (ex-DBConnect)
 *      ├── application/
 *      │   └── ContactService.php (logique métier)
 *      ├── presentation/
 *      │   ├── cli/
 *      │   │   ├── CommandParser.php
 *      │   │   └── CliController.php (ex-Command)
 *      │   └── ResponseFormatter.php
 *      ├── config/
 *      │   └── Conf.php
 *      └── exception/
 *
 */
class Command
{
    public function __construct(private \PDO $pdo)
    {

    }

    /**
     * Retrieves a list of all contacts and formats them as a string.
     *
     * @return string A formatted string containing the list of contacts.
     */
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

    /**
     * Retrieves and displays the contact details corresponding to the provided ID.
     *
     * @param int $id The ID of the contact to retrieve.
     * @return string Returns the contact details or an error message if the contact
     *                cannot be retrieved.
     */
    public function detail(int $id) : string
    {
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

    /**
     * Creates a new contact with the provided details and retrieves its information.
     *
     * @param string $name The name of the contact to create.
     * @param string $email (optional) The email address of the contact. Defaults to an empty string.
     * @param string $phone_number (optional) The phone number of the contact. Defaults to an empty string.
     * @return string Returns the details of the created contact or an error message if the
     *                contact cannot be created or retrieved.
     */
    public function create(string $name, string $email='', string $phone_number='') : string
    {
        $mng = new ContactManager($this->pdo);
        try {
            $contact = $mng->create($name, $email, $phone_number);
            $contact = $mng->find($contact);
        } catch (InsertContactException $e) {
            return "Erreur lors de l'insertion du contact : " . PHP_EOL . $e->getMessage() . PHP_EOL;
        } catch(ReadContactException $e) {
            return "Erreur lors de la récupération du contact." . PHP_EOL;
        } catch(\InvalidArgumentException $e) {
            return $e->getMessage() . PHP_EOL;
        }
        return "Contact créé : " . $contact . PHP_EOL;
    }

    /**
     * Updates the contact information with the given details for the specified ID.
     * Then return a confirmation or error message.
     *
     * @param int $id The ID of the contact to update.
     * @param string $name The new name for the contact (optional).
     * @param string $email The new email address for the contact (optional).
     * @param string $phone_number The new phone number for the contact (optional).
     * @return string Returns a confirmation message if the update is successful,
     *                or an error message if the contact does not exist or an
     *                error occurs during the update.
     */
    public function update(int $id, string $name='', string $email='', string $phone_number='') : string
    {
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

    /**
     * Deletes a contact identified by the provided ID from the database.
     *  Then return a confirmation or error message.
     *
     * @param int $id The ID of the contact to delete.
     * @return string Returns a success message if the contact was deleted,
     *                or an error message if the deletion failed or the contact
     *                does not exist.
     */
    public function delete(int $id) : string
    {
        $mng = new ContactManager($this->pdo);
        try {
            $contact = $mng->find($id);
            if(!$contact) {
                return "Ce contact n'existe pas en BDD." . PHP_EOL;
            }
            $mng->delete($id);
        } catch(\Exception $e) {
            return "Erreur lors de la suppression du contact : " . $e->getMessage() . PHP_EOL;
        }
        //On vérifie que le contact n'existe plus en BDD
        $contact = $mng->find($id);
        if($contact) {
            return "Erreur inconnue lors de la suppression du contact. Le contact est toujours présent en BDD." . PHP_EOL;
        }
        return "Contact supprimé." . PHP_EOL;
    }

}