<?php

namespace ToDoListApi;

/**
 * Class Auth
 *
 * A class to authenticate a user by its API key
 */
readonly class Auth
{
    private int $user_id;

    /**
     * Auth constructor
     *
     * @param UserGateway $user_gateway The gateway to the user table to query the database
     */
    public function __construct(private UserGateway $user_gateway)
    {

    }

    /**
     * Checks the X-API-KEY in the request headers
     *
     * @return bool Returns false if the X-API-KEY request header is empty, not set,
     * or if there is not a user in the database with the sent api key. Returns true
     * if a matching record was found.
     */
    public function authenticateAPIKey(): bool
    {
        // If the X-API-KEY header is not sent or if it's empty, send a 400 Bad Request response code and message
        if (empty($_SERVER['HTTP_X_API_KEY'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing API key']);
            return false;
        }

        // Get the api key from the request headers
        $api_key = $_SERVER['HTTP_X_API_KEY'];

        // Try to get a user by the API key. If it's false, send a 401 Unauthorized response code and message.
        $user = $this->user_gateway->getByAPIKey($api_key);

        if ($user === false) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid API key']);
            return false;
        }

        $this->user_id = $user['id'];

        return true;

    }

    // Return the user id
    public function getUserID(): int
    {
        return $this->user_id;
    }

}