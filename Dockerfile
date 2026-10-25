FROM php:8.2-fpm

# Замена репозиториев на Яндекс-зеркало для стабильности в России
RUN sed -i 's|deb.debian.org|mirror.yandex.ru|g' /etc/apt/sources.list.d/debian.sources 2>/dev/null || \
    sed -i 's|deb.debian.org|mirror.yandex.ru|g' /etc/apt/sources.list 2>/dev/null || true

RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mysqli gd zip

RUN docker-php-ext-install opcache

# Установка Composer (современный способ)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Установка Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony 2>/dev/null || true

# Копирование файлов проекта
WORKDIR /var/www/project
COPY ./app/ .

# Установка зависимостей
RUN composer require symfony/maker-bundle --dev

EXPOSE 9000