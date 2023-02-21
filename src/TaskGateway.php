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
        $data = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $row['is_completed'] = (bool)$row['is_completed'];

            $data[] = $row;
        }
        return $data;
    }

    public function get(string $id): array|false
    {
        $sql = "SELECT *
                FROM task
                WHERE id = :id";
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($data !== false) {
            $data['is_completed'] = (bool)$data['is_completed'];
        }
        return $data;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO task (name, priority, is_completed)
                VALUES (:name, :priority, :is_completed)";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$data['name'], empty($data['priority']) ? NULL : $data['priority'], $data['is_completed'] ?? false]);

        return $this->conn->lastInsertId();
    }

    public function update(string $id, array $data): int
    {
        $fields = [];

        if (!empty($data['name'])) {
            $fields['name'] = $data['name'];

        }

        if (array_key_exists("priority", $data)) {
            $fields['priority'] = $data['priority'];
        }

        if (array_key_exists("is_completed", $data)) {
            $fields['is_completed'] = $data['is_completed'];
        }

        if (empty($fields)) {

            return 0;
        } else {

            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE task"
                . " SET " . implode(", ", $sets)
                . " WHERE id = :id";

            $fields['id'] = $id;
            $statement = $this->conn->prepare($sql);
            $statement->execute($fields);

            return $statement->rowCount();
        }
    }

    public function delete(string $id): int {
        $sql = "DELETE FROM task
                WHERE id = :id";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$id]);
        return $statement->rowCount();
    }
}