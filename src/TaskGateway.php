<?php

namespace ToDoListApi;

class TaskGateway
{
    private \PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT *
                FROM task
                ORDER BY name";
        $statement = $this->conn->query($sql);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}