FROM php:7.4-fpm-alpine

COPY . .
WORKDIR /analyze

ADD --chown=www-data:www-data . /analyze/

ADD https://getcomposer.org/download/1.6.2/composer.phar /usr/bin/composer
RUN chmod +x /usr/bin/composer
RUN composer install

ENTRYPOINT ["php", "-f", "./entry.php"]