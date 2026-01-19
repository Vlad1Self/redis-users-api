FROM php:8.4-cli AS builder
RUN apt-get update && apt-get install -y libpng-dev libonig-dev libzip-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /app
COPY composer.json composer.lock /app/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts
COPY . /app
RUN composer dump-autoload --optimize

FROM php:8.4-fpm
RUN apt-get update && apt-get install -y libpng-dev libonig-dev libzip-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www/html
COPY --from=builder /app /var/www/html
RUN mkdir -p storage/framework/{views,cache,sessions} storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache storage/logs \
    && chmod -R 775 storage bootstrap/cache storage/logs
USER www-data
EXPOSE 9000
CMD ["php-fpm"]
