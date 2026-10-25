<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

// CountryNotFoundException - исключение не найденной страны
class CountryNotFoundException extends Exception {

    public function __construct(string $notFoundCode, Throwable $previous = null) {
        $exceptionMessage = "country with code '". $notFoundCode ."' not found";
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::NOT_FOUND_ERROR,
            previous: $previous,
        );
    }
}

