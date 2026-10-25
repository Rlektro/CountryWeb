<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

// DuplicatedNameException - исключение дублирующегося наименования страны
class DuplicatedNameException extends Exception {

    public function __construct(string $duplicatedName, Throwable $previous = null) {
        $exceptionMessage = "country name '". $duplicatedName ."' is duplicated";
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::DUPLICATED_NAME_ERROR,
            previous: $previous,
        );
    }
}

