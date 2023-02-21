<?php

namespace ToDoListApi;

class ErrorHandler
{

    public static function handleException(\Throwable $exception): void
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
     * @throws \ErrorException
     */
    public static function handleError(int $severity, string $message, string $file, int $line): void
    {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

}