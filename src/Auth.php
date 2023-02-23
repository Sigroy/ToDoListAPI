<?php

namespace ToDoListApi;

class Auth
{
    public function __construct(private readonly UserGateway $user_gateway)
    {

    }

    public function authenticateAPIKey(): bool
    {
        if (empty($_SERVER['HTTP_X_API_KEY'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing API key']);
            return false;
        }

        $api_key = $_SERVER['HTTP_X_API_KEY'];

        if ($this->user_gateway->getByAPIKey($api_key) === false) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid API key']);
            return false;
        }

        return true;

    }

}