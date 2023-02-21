<?php

namespace ToDoListApi;

class TaskController
{

    public function __construct(private TaskGateway $gateway)
    {
    }

    public function processRequest(string $method, ?string $id): void
    {
        if ($id === null || $id === '') {
            if ($method === 'GET') {
                echo json_encode($this->gateway->getAll());
            } elseif ($method === 'POST') {
                $data = (array)json_decode(file_get_contents("php://input"), true);
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

            $task = $this->gateway->get($id);
            if ($task === false) {
                $this->respondNotFound($id);
                return;
            }

            switch ($method) {
                case 'GET':
                    echo json_encode($task);
                    break;
                case 'PUT':
                case 'PATCH':
                    $data = (array)json_decode(file_get_contents("php://input"), true);
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

    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    private function respondNotFound(string $id): void
    {
        http_response_code(404);
        echo json_encode(['message' => "Task with ID $id not found"]);
    }

    private function respondCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Task created successfully", "id" => $id]);
    }

    private function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }

    private function getValidationErrors(array $data, bool $isNew = true): array
    {
        $errors = [];

        if ($isNew && empty($data["name"])) {
            $errors['name'] = "Required.";
        }

        if (!empty($data['priority'])) {
            if (filter_var($data['priority'], FILTER_VALIDATE_INT) === false) {
                $errors['priority'] = "Must be an integer.";
            }
        }
        return $errors;
    }
}