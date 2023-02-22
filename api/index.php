<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$parts = explode("/", $path);

$resource = $parts[3];

$id = $parts[4] ?? null;

if ($resource != 'tasks') {
//    header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found", response_code: 404);
    http_response_code(404);
    exit;
}

$database = new \ToDoListApi\Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

$user_gateway = new \ToDoListApi\UserGateway($database);

$auth = new \ToDoListApi\Auth($user_gateway);

if (!$auth->authenticateAPIKey()) exit;

$task_gateway = new \ToDoListApi\TaskGateway($database);

$controller = new \ToDoListApi\TaskController($task_gateway);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);