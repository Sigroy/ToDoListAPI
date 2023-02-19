<?php

namespace ToDoListApi;

class TaskController
{

    public function processRequest(string $method, ?string $id): void
    {
        if ($id === null || $id === '') {
            if ($method === 'GET') {
                echo 'index';
            } elseif ($method === 'POST') {
                echo 'create';
            }
        } else {
            switch ($method) {
                case 'GET':
                    echo "show $id";
                    break;
                case 'PUT':
                case 'PATCH':
                    echo "update $id";
                    break;
                case 'DELETE':
                    echo "delete $id";
                    break;
            }
        }
    }
}