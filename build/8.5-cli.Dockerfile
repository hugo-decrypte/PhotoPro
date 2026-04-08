# Base PHP
FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions installer
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

# PHP config
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install PHP extensions (clean group)
RUN install-php-extensions \
    pdo \
    pdo_pgsql \
    pgsql \
    intl \
    sockets \
    zip \
    opcache \
    xdebug

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Workdir
WORKDIR /var/php

# Expose port
EXPOSE 80

# Default command (overridden by docker-compose)
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]