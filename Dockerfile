FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":80"

WORKDIR /app

COPY . /app

RUN apt update && apt install -y zip libzip-dev libpq-dev && \
    docker-php-ext-install zip pdo pdo_mysql && \
    docker-php-ext-enable zip pdo pdo_mysql

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader
