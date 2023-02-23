<?php

namespace ToDoListApi;

use ErrorException;
use Throwable;

class ErrorHandler
{
    /**
     * Handle exceptions by logging the exception message, sending a 500 response code to the client
     * and outputting a JSON response with information about the exception
     * @param Throwable $exception
     * @return void
     */
    public static function handleException(Throwable $exception): void
    {
        error_log($exception->getMessage());
        http_response_code(500);
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
        ]);
    }

    /**
     * Handle errors by converting them into exceptions using the ErrorException class and throwing them
     * so that they can be handled by the handleException method
     * @param int $severity
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     * @throws ErrorException
     */
    public static function handleError(int $severity, string $message, string $file, int $line): void
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

}