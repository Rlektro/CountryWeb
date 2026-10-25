<?php
namespace App\Rdb;

use App\Model\Country;
use App\Model\CountryRepository;
use App\Model\Exceptions\{
DuplicatedNameException,
InvalidCodeException,
CountryNotFoundException,
DuplicatedCodeException,
ErrorCodes,
ValidationException,
};
use Override;
Use Exception;

class CountryStorage implements CountryRepository{
    public function __construct(
    private SqlHelper $SqlHelper
    )
    {    }
    #[Override]
    public function SelectAll(): array
    {
        try{
            $connection = $this -> SqlHelper->openDbConnection();
            $queryStr= '
                SELECT short_name_f,
                full_name_f,
                iso_alpha2_f,
                iso_alpha3_f,
                iso_numeric_f,
                population_f,
                square_f
                FROM country_t' ;
            $rows = $connection -> query(query: $queryStr);

            $countries = [];
            while( $row = $rows ->fetch_array()){
                $country = new Country(
                shortName: $row[0],
                fullName: $row[1],
                isoAlpha2: $row[2],
                isoAlpha3: $row[3],    
                isoNumeric: $row[4],    
                population: $row[5],    
                square: $row[6],    
                );
                array_push($countries, $country);
            }
            return $countries;
        } finally {
            if(isset($connection)){
            $connection->close();
            }
        }
    }
    #[Override]
    public function SelectByCode(string $code): ?Country
    {
        try{
            $connection = $this -> SqlHelper-> openDbConnection();
            $queryStr = ' SELECT short_name_f,
                full_name_f,
                iso_alpha2_f,
                iso_alpha3_f,
                iso_numeric_f,
                population_f,
                square_f 
                FROM country_t';
            if (preg_match('/^[A-Z]{2}$/', $code)) {
                $column = 'iso_alpha2_f';
            } elseif (preg_match('/^[A-Z]{3}$/', $code)) {
                $column = 'iso_alpha3_f';
            } elseif (preg_match('/^[0-9]{3}$/', $code)) {
                $column = 'iso_numeric_f';
            } else {
                throw(new InvalidCodeException($code));
            }
            $queryStr = $queryStr . " WHERE $column = '$code';";
            $rows = $connection -> query(query: $queryStr);
            while ($row = $rows->fetch_array()) {
                return new Country(
                shortName: $row[0],
                fullName: $row[1],
                isoAlpha2: $row[2],
                isoAlpha3: $row[3],    
                isoNumeric: $row[4],    
                population: $row[5],    
                square: $row[6],    
                );
            }
            return null;
        } finally {
            if(isset($connection)){
            $connection->close();
            }
        }
    }
    #[Override]
    public function SelectByName(string $name): ?Country
    {
        try{
            $connection = $this -> SqlHelper-> openDbConnection();
            $queryStr = "SELECT short_name_f, full_name_f, iso_alpha2_f, iso_alpha3_f, iso_numeric_f, population_f, square_f 
                     FROM country_t 
                     WHERE short_name_f = '$name' OR full_name_f = '$name' 
                     LIMIT 1;";
            $rows = $connection -> query(query: $queryStr);
            while ($row = $rows->fetch_array()) {
                return new Country(
                shortName: $row[0],
                fullName: $row[1],
                isoAlpha2: $row[2],
                isoAlpha3: $row[3],    
                isoNumeric: $row[4],    
                population: $row[5],    
                square: $row[6],    
                );
            }
            return null;
        } finally {
            if(isset($connection)){
            $connection->close();
            }
        }
    }
    #[Override]
    public function store(Country $country): void
    {
        try{
            $connection = $this->SqlHelper->openDbConnection();
            $queryStr = 'INSERT INTO country_t (
                short_name_f,
                full_name_f,
                iso_alpha2_f,
                iso_alpha3_f,
                iso_numeric_f,
                population_f,
                square_f
                )   VALUES (?, ?, ?, ?, ?, ?, ?);';
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param(
                'sssssid', 
                $country->shortName, 
                $country->fullName,
                $country->isoAlpha2, 
                $country->isoAlpha3, 
                $country->isoNumeric, 
                $country->population, 
                $country->square, 
            );
            // выполнить запрос
            if (!$query->execute()) {
                throw new Exception(message: 'insert execute failed');
            }
        } finally {
            if(isset($connection)){
            $connection->close();
            }
        }
    }
    #[Override]
    public function delete(string $code): void
    {
        try {
            $connection = $this->SqlHelper->openDbConnection();
            // подготовить запрос INSERT
            if (preg_match('/^[A-Z]{2}$/', $code)) {
                $column = 'iso_alpha2_f';
            } elseif (preg_match('/^[A-Z]{3}$/', $code)) {
                $column = 'iso_alpha3_f';
            } elseif (preg_match('/^[0-9]{3}$/', $code)) {
                $column = 'iso_numeric_f';
            } else {
                throw(new InvalidCodeException($code));
            }
            $queryStr = "DELETE FROM country_t WHERE $column = ?;";

            $query = $connection->prepare(query: $queryStr);
            $query->bind_param('s', $code);
            // выполнить запрос
            if (!$query->execute()) {
                throw new Exception(message: 'delete execute failed');
            }
        } finally {
            // в конце в любом случае закрыть соединение с БД если оно было открыто
            if (isset($connection)) {
                $connection->close();
            }
        }
    }
    #[Override]
    public function update(string $code, Country $country): void
    {
        try{
            $connection = $this->SqlHelper->openDbConnection();
            if (preg_match('/^[A-Z]{2}$/', $code)) {
                $column = 'iso_alpha2_f';
            } elseif (preg_match('/^[A-Z]{3}$/', $code)) {
                $column = 'iso_alpha3_f';
            } elseif (preg_match('/^[0-9]{3}$/', $code)) {
                $column = 'iso_numeric_f';
            } else {
                throw new InvalidCodeException($code);
            }
            $queryStr = "UPDATE country_t SET 
                short_name_f = ?, 
                full_name_f = ?, 
                population_f = ?, 
                square_f = ? 
            WHERE $column = ?;";
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param(
                'ssids', 
                $country->shortName, 
                $country->fullName,
                $country->population, 
                $country->square,
                $code 
            );
            // выполнить запрос
            if (!$query->execute()) {
                throw new Exception(message: 'update execute failed');
            }
        } finally {
            if(isset($connection)){
            $connection->close();
            }
        }
    }
}