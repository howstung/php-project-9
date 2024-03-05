<?php

namespace App\Database;

abstract class DBManager
{
    protected \PDO $pdo;

    public function __construct(Connection $connection)
    {
        $this->pdo = $connection->getPdo();
        return $this;
    }
}
