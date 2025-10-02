FROM php:8.2-cli

# MySQL PDO sürücüsünü yükle
RUN docker-php-ext-install pdo pdo_mysql

# Composer yükle
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Git ve unzip yükle (composer dependencies için)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html