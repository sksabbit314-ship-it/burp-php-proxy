FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install zip sockets pcntl

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html/

RUN mkdir -p /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod 777 /var/www/html/storage

EXPOSE 80

CMD ["apache2-foreground"]
