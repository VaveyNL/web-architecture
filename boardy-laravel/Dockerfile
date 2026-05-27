FROM php:8.3-fpm

# Системные пакеты
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP-расширения
RUN docker-php-ext-install pdo_mysql mbstring zip opcache

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая папка
WORKDIR /var/www/html

# Сначала composer файлы - для кеширования слоёв
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader

# Потом весь проект
COPY . .

# Автозагрузка классов
RUN composer dump-autoload --optimize

# Права Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
