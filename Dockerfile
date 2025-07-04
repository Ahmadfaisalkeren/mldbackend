FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":80"

WORKDIR /app

COPY . /app

RUN apt update && apt install -y \
    zip \
    libzip-dev \
    libpq-dev \
    libpng-dev && \
    docker-php-ext-install zip pdo pdo_mysql gd && \
    docker-php-ext-enable zip pdo pdo_mysql gd

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader
