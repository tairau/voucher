FROM php:8.2-cli-alpine3.17

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql
