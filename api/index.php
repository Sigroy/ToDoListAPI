<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

set_exception_handler("\ToDoListApi\ErrorHandler::handleException");

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$parts = explode("/", $path);

$resource = $parts[3];

$id = $parts[4] ?? null;

if ($resource != 'tasks') {
//    header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found", response_code: 404);
    http_response_code(404);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

$controller = new \ToDoListApi\TaskController();

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);