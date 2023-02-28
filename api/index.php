<?php
declare(strict_types=1);

// Require bootstrap file to include initial config
require __DIR__ . '/bootstrap.php';

// Parse the URL of the request without the query string: /ToDoListAPI/api/tasks(/123)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Separate the string into different parts delimited by /
$parts = explode("/", $path);

// Assign the resource name (tasks)
$resource = $parts[3];

// Assign the id if it's set in the array, otherwise assign null
$id = $parts[4] ?? null;

// If the resource is different from "tasks", respond with 404 Not Found and exit the script
if ($resource != 'tasks') {
//    header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found", response_code: 404);
    http_response_code(404);
    exit;
}

// Create instance of the Database class using the data in the .env file
$database = new \ToDoListApi\Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

// Create a gateway to query the user table with the database
$user_gateway = new \ToDoListApi\UserGateway($database);

$codec = new \ToDoListApi\JWTCodec($_ENV["SECRET_KEY"]);

// Create instance of the authentication class using the user gateway
$auth = new \ToDoListApi\Auth($user_gateway, $codec);

// With the auth class, authenticate that there is a user in the db with the api key sent in the X-API-KEY request header
// If there is not, or if the X-API-KEY request is empty, exit the script.
//if (!$auth->authenticateAPIKey()) exit;
if (!$auth->authenticateAccessToken()) exit;

// Get user id if authentication passes
$user_id = $auth->getUserID();

// Create a gateway to query the task table with the database
$task_gateway = new \ToDoListApi\TaskGateway($database);

// Create an instance of the controller of tasks using the task gateway and user id
$controller = new \ToDoListApi\TaskController($task_gateway, $user_id);

// Process the request according depending on the action (request method) and if and id was provided in the URL
$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);