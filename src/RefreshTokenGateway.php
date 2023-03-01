<?php

namespace ToDoListApi;

class RefreshTokenGateway
{

    private \PDO $conn;
    private string $key;

    public function __construct(Database $database, string $key)
    {
        $this->conn = $database->getConnection();
        $this->key = $key;
    }

    public function create(string $token, int $expiry): bool
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "INSERT INTO refresh_token (token_hash, expires_at)
                VALUES (:token_hash, :expires_at);";

        $statement = $this->conn->prepare($sql);

        return $statement->execute([$hash, $expiry]);
    }

    public function delete(string $token): int
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "DELETE FROM refresh_token
                WHERE token_hash = :token_hash;";

        $statement = $this->conn->prepare($sql);

        $statement->execute(['token_hash' => $hash]);

        return $statement->rowCount();
    }

    public function getByToken(string $token): array|false
    {
        $hash = hash_hmac("sha256", $token, $this->key);
        $sql = "SELECT *
                FROM refresh_token
                WHERE token_hash = :token_hash;";
        $statement = $this->conn->prepare($sql);
        $statement->execute([$hash]);
        return $statement->fetch();
    }

    public function deleteExpired(): int {
        $sql = "DELETE FROM refresh_token
                WHERE expires_at < UNIX_TIMESTAP();";
        $statement = $this->conn->query($sql);

        return $statement->rowCount();
    }


}