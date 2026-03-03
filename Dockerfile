FROM composer:2 as vendor
WORKDIR /app
COPY composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

FROM php:8.2-fpm-alpine
RUN apk add --no-cache --virtual .php-rundeps \
    freetype libpng libjpeg-turbo libzip mysql-client \
    && apk add --no-cache --virtual .build-deps \
    freetype-dev libpng-dev libjpeg-turbo-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli gd zip \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache
EXPOSE 9000
CMD ["php-fpm"]
