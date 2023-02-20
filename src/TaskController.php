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
                echo 'create';
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
                    echo "update $id";
                    break;
                case 'DELETE':
                    echo "delete $id";
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
}