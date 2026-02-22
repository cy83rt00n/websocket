<?php

namespace App\Core\DB;

use PDO;
use PDOException;
use Exception;
use App\Core\Interfaces\ConfigInterface;
use App\Core\Interfaces\DBInterface;
use App\Core\Interfaces\DumpableInterface;

class Mysql implements DBInterface, DumpableInterface
{
    public const CONFIG_KEY_NOT_FOUND_EXCEPTION = "Config key for Mysql DB not found";
    public const DB_CONNECTION_EXCEPTION = "Database connetction error: ";
    public const DB_CONNECTION_NOT_INITED_EXCEPTION = "Database connetction not inited!";

    private ?PDO $connection = null;

    public function init(ConfigInterface $config): PDO
    {
        if (!$config->has("db.mysql")) {
            throw new Exception(self::CONFIG_KEY_NOT_FOUND_EXCEPTION);
        }
        try {
            if (!$this->connection) {
                $this->connection = new PDO(sprintf(
                    "mysql:host=%s;dbname=%s;charset=utf8mb4;",
                    $config->get("db.mysql.host"),
                    $config->get("db.mysql.name")
                ), $config->get("db.mysql.user"), $config->get("db.mysql.password"));
            }
            return $this->connection;
        } catch (PDOException $ex) {
            throw new Exception(self::DB_CONNECTION_EXCEPTION . $ex->getMessage());
        }
    }

    public function getConnection()
    {
        if (!$this->connection) {
            throw new Exception(self::DB_CONNECTION_NOT_INITED_EXCEPTION);
        }
        return $this->connection;
    }

    public function close(): void
    {
        if (!$this->connection) {
            throw new Exception(self::DB_CONNECTION_NOT_INITED_EXCEPTION);
        }
        $this->connection = null;
    }

    public function dump()
    {
        var_dump($this->connection);
    }
}
