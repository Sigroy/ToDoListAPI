<?php

namespace ToDoListApi;

class Database
{
    public function __construct(private string $host, private string $name, private string $user, private string $password)
    {

    }

    public function getConnection(): \PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8;port=3307";
        $pdo = new \PDO($dsn, $this->user, $this->password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);
        return $pdo;
    }
}