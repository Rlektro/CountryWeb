<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

// DuplicatedCodeException - исключение дублирующегося кода страны
class DuplicatedCodeException extends Exception {

    public function __construct(string $duplicatedCode, Throwable $previous = null) {
        $exceptionMessage = "country code '". $duplicatedCode ."' is duplicated";
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::DUPLICATED_CODE_ERROR,
            previous: $previous,
        );
    }
}

