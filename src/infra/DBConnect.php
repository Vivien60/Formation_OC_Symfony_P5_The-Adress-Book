<?php
declare(strict_types=1);
namespace infra;

use config\Conf;
use PDO;

class DBConnect
{
    private static ?self $instance = null;
    private ?PDO $pdo = null;

    private function __construct(private array $conf)
    {

    }

    public static function fromInstance(array $conf): DBConnect
    {
        if(self::$instance !== null) {
            return self::$instance;
        }
        return new DBConnect($conf);
    }

    public function getPDO(): \PDO
    {
        if(!$this->pdo) {
            $this->connect();
        }
        return $this->pdo;
    }

    private function connect()
    {
        try {
            $this->pdo = new PDO(
                $this->conf["dsn"],
                $this->conf["user"],
                $this->conf["password"],
            );
        } catch (\Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
}