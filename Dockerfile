FROM php:8.1-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install

EXPOSE 8000
CMD ["php-fpm"]
