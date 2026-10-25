<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

// ValidationException - исключение ошибки валидации данных страны
class ValidationException extends Exception {

    public function __construct(string $message, Throwable $previous = null) {
        $exceptionMessage = "country validation failed: " . $message;
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::VALIDATION_ERROR,
            previous: $previous,
        );
    }
}

