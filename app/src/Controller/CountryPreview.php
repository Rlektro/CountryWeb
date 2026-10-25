<?php

namespace App\Controller;

// AirportPreview - легковесный класс превью аэропорта
class CountryPreview {

    public function __construct(
        public string $name,
        public string $uri,
    ) {}
}