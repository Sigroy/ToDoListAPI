<?php

namespace ToDoListApi;

class UserGateway
{
    private \PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getByAPIKey(string $key): array|false
    {
        $sql = "SELECT *
                FROM user
                WHERE api_key = :key_api";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$key]);
        return $statement->fetch();
    }
}