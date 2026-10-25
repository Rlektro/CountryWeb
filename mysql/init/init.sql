--починил текст
SET NAMES 'utf8mb4';
DROP TABLE IF EXISTS country_t;
CREATE TABLE country_t (
    id INT NOT NULL AUTO_INCREMENT,
    short_name_f VARCHAR(100) NOT NULL,
    full_name_f VARCHAR(255) NOT NULL,
    iso_alpha2_f CHAR(2) NOT NULL,
    iso_alpha3_f CHAR(3) NOT NULL,
    iso_numeric_f CHAR(3) NOT NULL,
    population_f BIGINT UNSIGNED NOT NULL,
    square_f DOUBLE NOT NULL,
    
    PRIMARY KEY (id),
    UNIQUE (short_name_f),
    UNIQUE (full_name_f),
    UNIQUE (iso_alpha2_f),
    UNIQUE (iso_alpha3_f),
    UNIQUE (iso_numeric_f)
--починил текст
)CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; 

-- 3. Заполнение тестовыми данными 
INSERT INTO country_t (
    short_name_f,
    full_name_f,
    iso_alpha2_f,
    iso_alpha3_f,
    iso_numeric_f,
    population_f,
    square_f
) VALUES 
    ('Россия', 'Российская Федерация', 'RU', 'RUS', '643', 146150789, 17125191.0),
    ('Китай', 'Китайская Народная Республика', 'CN', 'CHN', '156', 1409670000, 9596961.0),
    ('США', 'Соединенные Штаты Америки', 'US', 'USA', '840', 334914895, 9833517.0),
    ('Германия', 'Федеративная Республика Германия', 'DE', 'DEU', '276', 84358845, 357588.0),
    ('Франция', 'Французская Республика', 'FR', 'FRA', '250', 68042591, 643801.0),
    ('Япония', 'Государство Япония', 'JP', 'JPN', '392', 124500000, 377975.0),
    ('Бразилия', 'Федеративная Республика Бразилия', 'BR', 'BRA', '076', 203062512, 8515767.0);