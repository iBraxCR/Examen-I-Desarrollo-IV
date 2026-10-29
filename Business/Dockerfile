FROM php:8.5-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libonig-dev \
    && docker-php-ext-install mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY ./Business/composer.* ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
COPY ./Business/src ./src
COPY ./Business/public ./public
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' /etc/apache2/sites-available/000-default.conf
EXPOSE 80
