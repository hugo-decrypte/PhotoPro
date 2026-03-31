# Use an official PHP runtime as a base image
FROM php:8.5-cli

RUN apt-get update&& \
    apt-get install --force-yes \
    unzip

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN install-php-extensions  iconv intl sockets
RUN install-php-extensions  pgsql mysqli
RUN install-php-extensions  pdo_mysql pdo_pgsql
RUN install-php-extensions  xdebug

COPY --from=composer/composer:2.9.3 /usr/bin/composer /usr/bin/composer

EXPOSE 80