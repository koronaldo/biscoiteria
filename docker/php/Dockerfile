FROM php:8.4-fpm

# Atualiza e instala dependências essenciais do PHP
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip libicu-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo_pgsql zip gd

# Instalar Node 20 e NPM
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Copiar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Diretórios de storage do Laravel (Livewire tmp, logs, cache, etc.)
RUN mkdir -p \
    /var/www/html/storage/app/private/livewire-tmp \
    /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/zz-uploads.ini
COPY docker/php/fpm-pool.conf /usr/local/etc/php-fpm.d/zz-tuning.conf

WORKDIR /var/www/html
