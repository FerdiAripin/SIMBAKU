FROM php:8.1-fpm

# Install ekstensi penting (mysql, gd, dsb.)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli gd zip

# Optional: Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
