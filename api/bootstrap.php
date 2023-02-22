<?php

require dirname(__DIR__) . '/vendor/autoload.php';

set_error_handler("\ToDoListApi\ErrorHandler::handleError");
set_exception_handler("\ToDoListApi\ErrorHandler::handleException");

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

header("Content-Type: application/json; charset=UTF-8");