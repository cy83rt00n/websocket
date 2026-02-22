<?php

namespace App\Core\DB;

use PDO;
use PDOException;
use Exception;
use App\Core\Interfaces\ConfigInterface;
use App\Core\Interfaces\DBInterface;
use App\Core\Interfaces\DumpableInterface;

class Postgres implements DBInterface, DumpableInterface
{
    public const CONFIG_KEY_NOT_FOUND_EXCEPTION = "Config key for pgsql DB not found";
    public const DB_CONNECTION_EXCEPTION = "Database connetction error: ";
    public const DB_CONNECTION_NOT_INITED_EXCEPTION = "Database connetction not inited!";

    private ?PDO $connection = null;

    public function __construct(ConfigInterface $config)
    {
        return $this->init($config);
    }

    public function init(ConfigInterface $config): PDO
    {
        if (!$config->has("db.pgsql")) {
            throw new Exception(self::CONFIG_KEY_NOT_FOUND_EXCEPTION);
        }
        try {
            if (!$this->connection) {
                $this->connection = new PDO(sprintf(
                    "pgsql:host=%s;dbname=%s;",
                    $config->get("db.pgsql.host"),
                    $config->get("db.pgsql.name")
                ), $config->get("db.pgsql.user"), $config->get("db.pgsql.password"));
            }
            return $this->connection;
        } catch (PDOException $ex) {
            throw new Exception(self::DB_CONNECTION_EXCEPTION . $ex->getMessage());
        }
    }

    public function getConnection(): PDO
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
