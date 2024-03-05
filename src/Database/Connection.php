<?php

namespace App\Database;

class Connection
{
    private \PDO $pdo;

    public function __construct()
    {
        $databaseUrl = parse_url($_ENV['DATABASE_URL']);
        $provider = $databaseUrl['scheme'];
        $username = $databaseUrl['user'];
        $password = $databaseUrl['pass'];
        $host = $databaseUrl['host'];
        $port = $databaseUrl['port'];
        $dbName = ltrim($databaseUrl['path'], '/');

        $provider = 'pgsql';

        $connectString = "{$provider}:host={$host};port={$port};dbname={$dbName};user={$username};password=$password";
        $this->pdo = new \PDO($connectString);
        return $this->pdo;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}
