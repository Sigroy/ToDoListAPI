<?php

namespace ToDoListApi;

use PDO;

/**
 * Class UserGateway
 *
 * A gateway for retrieving user data from a database.
 */
class UserGateway
{
    /**
     * The PDO database connection instance.
     *
     * @var PDO
     */
    private PDO $conn;

    /**
     * UserGateway constructor.
     *
     * @param Database $database The database instance to use for the connection.
     */
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    /**
     * Get a user record by their API key.
     *
     * @param string $key The API key to search for.
     *
     * @return array|false Returns an array of user data if a matching record was found,
     *                     or false if no matching record was found.
     */
    public function getByAPIKey(string $key): array|false
    {
        $sql = "SELECT *
                FROM user
                WHERE api_key = :key_api";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$key]);
        return $statement->fetch();
    }

    public function getByUsername(string $username): array|false
    {
        $sql = "SELECT *
                FROM user
                WHERE username = :username";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$username]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getByID(int $id): array|false
    {
        $sql = "SELECT *
                FROM user
                WHERE id = :id";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$id]);
        return $statement->fetch();
    }
}