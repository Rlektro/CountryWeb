<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

// InvalidCodeException - исключение невалидного кода страны
class InvalidCodeException extends Exception {

    public function __construct(string $invalidCode, string $message = 'validation failed', Throwable $previous = null) {
        $exceptionMessage = "country code '". $invalidCode ."' is invalid: " . $message;
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::INVALID_CODE_ERROR,
            previous: $previous,
        );
    }
}

