<?php

namespace App\Model;

use Exception;
use App\Model\Exceptions\{
DuplicatedNameException,
InvalidCodeException,
CountryNotFoundException,
DuplicatedCodeException,
ErrorCodes,
ValidationException,
};
use App\Rdb\CountryStorage;

// CountryScenarios - класс сценариев бизнес-логики для работы со странами
class CountryScenarios
{
    public function __construct(
    private readonly CountryRepository  $storage
    ){
    }

    /**
     * getAll - получение списка всех стран
     * 
     * Вход: -
     * Выход: массив объектов Country
     * 
     * @return Country[] Список всех стран
     * @throws Exception
     */
    public function getAll(): array
    {
        return $this->storage->SelectAll();
    }
    
    /**
     * get - получение страны по коду
     * 
     * Вход: код страны (двухбуквенный, трехбуквенный или числовой)
     * Выход: объект Country
     * 
     * @param string $code Код страны (двухбуквенный, трехбуквенный или числовой)
     * @return Country Объект найденной страны
     * @throws InvalidCodeException Если формат кода невалиден
     * @throws CountryNotFoundException Если страна с таким кодом не найдена
     * @throws Exception
     */
    public function get(string $code): Country
    {
        if($country = $this->storage->SelectByCode($code)){
            return $country;
        }
        throw new CountryNotFoundException($code);
    }

    /**
     * add - сохранение новой страны
     * 
     * Вход: объект Country
     * Выход: void
     * Ограничения: уникальные валидные коды, непустые уникальные названия,
     *              неотрицательные население и площадь.
     * 
     * @param Country $country Сохраняемый объект страны
     * @return void
     * @throws InvalidCodeException Если коды страны не соответствуют формату
     * @throws ValidationException Если не заполнены названия или отрицательные население/площадь
     * @throws DuplicatedCodeException Если страна с таким кодом уже существует
     * @throws DuplicatedNameException Если страна с таким названием уже существует
     * @throws Exception
     */
    public function store(Country $country): void
    {
        if(empty(trim($country->shortName))) throw new ValidationException($country->shortName);
        if(empty(trim($country->fullName))) throw new ValidationException($country->fullName);
        if($country->population < 0)throw new ValidationException($country->population);
        if($country->square < 0)throw new ValidationException($country->square);
        if (!preg_match('/^[A-Z]{2}$/', $country->isoAlpha2)) {
            throw new InvalidCodeException($country->isoAlpha2);
        }
        if (!preg_match('/^[A-Z]{3}$/', $country->isoAlpha3)) {
            throw new InvalidCodeException($country->isoAlpha3);
        }
        if (!preg_match('/^[0-9]{3}$/', $country->isoNumeric)) {
            throw new InvalidCodeException($country->isoNumeric);
        }
        if ($this->storage->selectByCode($country->isoAlpha2) !== null) {
        throw new DuplicatedCodeException($country->isoAlpha2);
        }
        if ($this->storage->selectByCode($country->isoAlpha3) !== null) {
            throw new DuplicatedCodeException($country->isoAlpha3);
        }
        if ($this->storage->selectByCode($country->isoNumeric) !== null) {
            throw new DuplicatedCodeException($country->isoNumeric);
        }
        if ($this->storage->selectByName($country->shortName) !== null) {
            throw new DuplicatedNameException($country->shortName);
        }
        if ($this->storage->selectByName($country->fullName) !== null) {
            throw new DuplicatedNameException($country->fullName);
        }
        $this->storage->store($country);
    }

    /**
     * edit - редактирование страны по коду
     * 
     * Вход: код страны ($code) и объект Country с новыми данными ($country)
     * Выход: void
     * Ограничения: можно редактировать все поля, кроме полей кодов.
     *              Новые значения проверяются на ограничения (уникальность, неотрицательность).
     * 
     * @param string $code Исходный код редактируемой страны
     * @param Country $country Объект страны с обновленными полями
     * @return void
     * @throws InvalidCodeException Если переданный код поиска невалиден
     * @throws CountryNotFoundException Если страна не найдена
     * @throws ValidationException Если новые поля не прошли валидацию
     * @throws DuplicatedNameException Если новое название конфликтует с другой страной
     * @throws Exception
     */
    public function edit(string $code, Country $country): void
    {
            $existingCountry = $this->storage->selectByCode($code);
        if ($existingCountry === null) {
            throw new CountryNotFoundException($code);
        }
                if (
            $country->isoAlpha2 !== $existingCountry->isoAlpha2 ||
            $country->isoAlpha3 !== $existingCountry->isoAlpha3 ||
            $country->isoNumeric !== $existingCountry->isoNumeric
        ) {
            throw new ValidationException('Country codes cannot be changed');
        }
        if(empty(trim($country->shortName))) throw new ValidationException($country->shortName);
        if(empty(trim($country->fullName))) throw new ValidationException($country->fullName);
        if($country->population < 0)throw new ValidationException($country->population);
        if($country->square < 0)throw new ValidationException($country->square);
        $foundByShort = $this->storage->selectByName($country->shortName);
        if ($foundByShort !== null && $foundByShort->isoAlpha3 !== $existingCountry->isoAlpha3) {
            throw new DuplicatedNameException($country->shortName);
        }

        $foundByFull = $this->storage->selectByName($country->fullName);
        if ($foundByFull !== null && $foundByFull->isoAlpha3 !== $existingCountry->isoAlpha3) {
            throw new DuplicatedNameException($country->fullName);
        }
        $this -> storage -> update($code,$country);
    }

    /**
     * delete - удаление страны по коду
     * 
     * Вход: код страны ($code)
     * Выход: void
     * 
     * @param string $code Код удаляемой страны (двухбуквенный, трехбуквенный или числовой)
     * @return void
     * @throws InvalidCodeException Если переданный код невалиден
     * @throws CountryNotFoundException Если страна с таким кодом не найдена
     * @throws Exception
     */
    public function delete(string $code): void
{
    $existing = $this->storage->selectByCode($code);
    if ($existing === null) {
        throw new CountryNotFoundException($code);
    }
    
    $this->storage->delete($code);
}
}
