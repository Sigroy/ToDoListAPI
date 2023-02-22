<?php

require __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $database = new ToDoListApi\Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

    $conn = $database->getConnection();

    $sql = "INSERT INTO user (name, username, password_hash, api_key)
            VALUES (:name, :username, :password_hash, :api_key);";

    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $api_key = bin2hex(random_bytes(16));

    $statement = $conn->prepare($sql);
    $statement->execute([$_POST['name'], $_POST['username'], $password_hash, $api_key]);

    echo "Thank you for registering. Your API key is ", $api_key;
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
    <title>Register</title>
</head>
<body>
<main class="container">
    <h1>Register</h1>
    <form method="post">
        <label for="name">
            Name
            <input type="text" name="name" id="name">
        </label>
        <label for="username">
            Username
            <input type="text" name="username" id="username">
        </label>
        <label for="password">
            Password
            <input type="text" name="password" id="password">
        </label>
        <button type="submit">Register</button>
    </form>
</main>
</body>
</html>
