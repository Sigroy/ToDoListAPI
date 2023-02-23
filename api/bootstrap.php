<?php

// Require file to automatically load class files
require dirname(__DIR__) . '/vendor/autoload.php';

// Set exception and error handlers
set_error_handler("\ToDoListApi\ErrorHandler::handleError");
set_exception_handler("\ToDoListApi\ErrorHandler::handleException");

// Create a dotenv instance with the database credentials and load it in the $_ENV array variable
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Set the content-type to json for the whole api
header("Content-Type: application/json; charset=UTF-8");