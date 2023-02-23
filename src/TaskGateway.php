<?php

namespace ToDoListApi;

use PDO;

/**
 * Class TaskGateway
 *
 * A gateway to retrieve tasks from the database
 */
class TaskGateway
{
    /**
     * The PDO database connection instance.
     *
     * @var PDO
     */
    private PDO $conn;

    /**
     * TaskGateway constructor
     *
     * @param Database $database The database instance to use for the connection.
     */
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    /**
     * Get all task records from the database
     *
     * @return array Returns an array with all the task records, or an empty array if there are no records
     */
    public function getAll(): array
    {
        $sql = "SELECT *
                FROM task
                ORDER BY name";
        $statement = $this->conn->query($sql);
        $data = [];

        // While fetching the rows, typecast the is_completed column to boolean
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $row['is_completed'] = (bool)$row['is_completed'];

            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get a task by an id
     * @param string $id The id to fetch in the database
     * @return array|false Returns an array with the data of the task, or false if there wasn't a match found
     */
    public function get(string $id): array|false
    {
        $sql = "SELECT *
                FROM task
                WHERE id = :id";
        $statement = $this->conn->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        // If there is a match, typecast the is_completed column to boolean
        if ($data !== false) {
            $data['is_completed'] = (bool)$data['is_completed'];
        }
        return $data;
    }

    /**
     * Insert a task record into the database
     *
     * @param array $data The task data to be inserted
     * @return string Returns a string id representing the row ID of the last row that was inserted into the database.
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO task (name, priority, is_completed)
                VALUES (:name, :priority, :is_completed)";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$data['name'], empty($data['priority']) ? NULL : $data['priority'], $data['is_completed'] ?? false]);

        return $this->conn->lastInsertId();
    }

    /**
     * Update a task record in the database
     * @param string $id The id of the task to update
     * @param array $data The data to update
     * @return int Returns the number of changed rows
     */
    public function update(string $id, array $data): int
    {
        // Create an empty array to store the sent data
        $fields = [];

        // If name is not empty, store it in fields
        if (!empty($data['name'])) {
            $fields['name'] = $data['name'];

        }

        // If the priority key exists in data, store it in fields
        if (array_key_exists("priority", $data)) {
            $fields['priority'] = $data['priority'];
        }

        // If the is_completed key exists in data, store it in fields
        if (array_key_exists("is_completed", $data)) {
            $fields['is_completed'] = $data['is_completed'];
        }

        // If fields is empty, return 0 and don't update anything
        if (empty($fields)) {

            return 0;
        } else {

            // Make an array with the sent data for the SQL prepare statement, if name and priority the array would be:
            // [0] => "name = :name",
            // [1] => "priority = :priority"
            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));

            // Dynamically make the SQL statement with the implode function to separate each element of the array
            // with ", "
            // "SET name = :name, priority = :priority
            $sql = "UPDATE task"
                . " SET " . implode(", ", $sets)
                . " WHERE id = :id";

            // Add an id key to the fields array with the passed id value
            $fields['id'] = $id;
            // Prepare and execute the SQL statement with the values in the fields array
            $statement = $this->conn->prepare($sql);
            $statement->execute($fields);

            // Return the number of changed rows
            return $statement->rowCount();
        }
    }

    /**
     * Delete a task record from the database
     * @param string $id The id of the record to delete
     * @return int Returns the number of changed rows
     */
    public function delete(string $id): int
    {
        $sql = "DELETE FROM task
                WHERE id = :id";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$id]);
        return $statement->rowCount();
    }
}