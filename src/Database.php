<?php

namespace ToDoListApi;

use PDO;
use PDOException;

class Database
{
    /**
     * Represents a database object with the specified database credentials.
     * @param string $host The database host name or IP address.
     * @param string $name The name of the database to connect to.
     * @param string $user The username to authenticate with the database.
     * @param string $password The password to authenticate with the database.
     */
    public function __construct(private string $host, private string $name, private string $user, private string $password)
    {

    }

    /**
     * Creates and returns a PDO connection to the database.
     * @return PDO Returns a PDO instance representing a connection to the database.
     * @throws PDOException If a PDOException occurs while attempting to connect to the database.
     */
    public function getConnection(): PDO
    {
        // The database DSN
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8;port=3307";

        // Create a new PDO instance with error handling options.
        $pdo = new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);

        // Return the PDO instance
        return $pdo;
    }
}