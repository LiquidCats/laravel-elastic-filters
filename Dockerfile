FROM php:7.4-alpine AS php

RUN apk update
RUN apk add --no-cache $PHPIZE_DEPS composer postgresql-dev git
RUN docker-php-source extract
RUN pecl install redis
RUN docker-php-ext-install pdo_pgsql bcmath
RUN docker-php-ext-enable pdo_pgsql redis bcmath
RUN docker-php-source delete

ADD ./ /var/www/filters
WORKDIR /var/www/filters

RUN composer global require hirak/prestissimo
RUN composer install