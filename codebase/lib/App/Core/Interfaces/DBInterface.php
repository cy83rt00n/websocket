<?php

namespace App\Core\Interfaces;

use PDO;

interface DBInterface
{
    public function init(ConfigInterface $config): PDO;
    public function getConnection(): PDO;
}
