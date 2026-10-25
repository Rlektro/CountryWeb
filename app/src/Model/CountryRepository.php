<?php
namespace App\Model;
interface CountryRepository{
    function SelectAll():array;
    function selectByCode(string $code): ?Country;
    function selectByName(string $name): ?Country;
    function store(Country $country): void;
    function delete(string $code):void;
    function update(string $code, Country $country) : void;
}
