<?php

namespace ToDoListApi;

/**
 * Class TaskController
 * This class handles incoming requests for tasks and processes them accordingly
 * @readonly
 */
readonly class TaskController
{

    /**
     * TaskController constructor
     * @param TaskGateway $gateway the task gateway object to handle task-related database operations
     */
    public function __construct(private TaskGateway $gateway)
    {
    }

    /**
     * Processes the incoming request for tasks and performs the necessary actions based on the request method and task ID
     * @param string $method The HTTP method of the request
     * @param string|null $id The ID of the requested task, or null if all tasks are requested
     * @return void
     */
    public function processRequest(string $method, ?string $id): void
    {
        // Check if the id is null or an empty string, which means that the user is either requesting for
        // all the task records or creating a new one
        if ($id === null || $id === '') {
            if ($method === 'GET') {
                // Output all the task records as json
                echo json_encode($this->gateway->getAll());
            } elseif ($method === 'POST') {
                // Retrieves the data from the request body, and decodes it from JSON format to an array.
                $data = (array)json_decode(file_get_contents("php://input"), true);
                // Validates the decoded data from the request body using the getValidationErrors method
                // and assigns any validation errors to the $errors array.
                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
                    $this->respondUnprocessableEntity($errors);
                    return;
                }
                $id = $this->gateway->create($data);
                $this->respondCreated($id);
            } else {
                $this->respondMethodNotAllowed('GET, POST');
            }
        } else {

            // Retrieve the task using the $id. If it doesn't exist, respond with a 404 Not Found status code.
            $task = $this->gateway->get($id);
            if ($task === false) {
                $this->respondNotFound($id);
                return;
            }

            switch ($method) {
                case 'GET':
                    // Output the task as json
                    echo json_encode($task);
                    break;
                case 'PUT':
                case 'PATCH':
                    // Retrieves the data from the request body, and decodes it from JSON format to an array.
                    $data = (array)json_decode(file_get_contents("php://input"), true);
                    // Validates the decoded data from the request body using the getValidationErrors method
                    // and assigns any validation errors to the $errors array.
                    $errors = $this->getValidationErrors($data, false);

                    if (!empty($errors)) {
                        $this->respondUnprocessableEntity($errors);
                        return;
                    }
                    $rows = $this->gateway->update($id, $data);
                    echo json_encode(["message" => "Task $id updated", "rows" => $rows]);
                    break;
                case 'DELETE':
                    $rows = $this->gateway->delete($id);
                    echo json_encode(["message" => "Task $id deleted", "rows" => $rows]);
                    break;
                default:
                    $this->respondMethodNotAllowed('GET, PUT, PATCH, DELETE');
                    break;
            }
        }
    }

    /**
     * Responds with an HTTP 405 Method Not Allowed error and an Allow header listing the allowed methods
     * @param string $allowed_methods A comma-separated list of allowed HTTP methods in uppercase
     * @return void
     */
    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    /**
     * Responds with an HTTP 404 Not Found error and a message indicating that the requested task was not found
     * @param string $id The ID of the requested task
     * @return void
     */
    private function respondNotFound(string $id): void
    {
        http_response_code(404);
        echo json_encode(['message' => "Task with ID $id not found"]);
    }


    /**
     * Responds with an HTTP 201 Created code and a message indicating that the task was successfully created
     * @param string $id The ID of the new task
     * @return void
     */
    private function respondCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Task created successfully", "id" => $id]);
    }

    /**
     * Responds with an HTTP 404 Unprocessable Entity error and a message indicating the errors
     * @param array $errors The array of errors
     * @return void
     */
    private function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }

    /**
     * This method is used to validate the data for creating or updating a task.
     * It takes an array of data and a boolean flag that specifies whether the data is for a new task or for updating.
     * The method checks if the data is valid and returns an array of error messages if there are any.
     * @param array $data The array of data to be validated.
     * @param bool $isNew The boolean flag that specifies whether the data is for a new task.
     * @return array The array of error messages if there are any, otherwise an empty array is returned.
     */
    private function getValidationErrors(array $data, bool $isNew = true): array
    {
        // Initialize an empty array to hold error messages.
        $errors = [];

        // Check if the name field is empty and the data is for a new task.
        // If so, add a 'Required' error message to the errors array for the name field.
        if ($isNew && empty($data["name"])) {
            $errors['name'] = "Required.";
        }

        // Check if the priority field is not empty.
        // If so, validate that the value is an integer using the filter_var() function.
        // If the value is not an integer, add a 'Must be an integer' error message to the errors array for the priority field.
        if (!empty($data['priority'])) {
            if (filter_var($data['priority'], FILTER_VALIDATE_INT) === false) {
                $errors['priority'] = "Must be an integer.";
            }
        }
        // Return the array of error messages, which may be empty if no errors were found.
        return $errors;
    }
}