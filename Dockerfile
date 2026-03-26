FROM php:8.2-apache AS base

RUN apt-get update && apt-get install -y \
        libicu-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        intl \
        opcache \
        zip \
        pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/app/public
RUN sed -ri 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' \
    /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ------- dependencies -------
FROM base AS deps

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction

# ------- build -------
FROM base AS build

COPY --from=deps /app/vendor vendor
COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative \
    && php bin/console asset-map:compile \
    && php bin/console tailwind:build --minify \
    && php bin/console cache:warmup --env=prod

# ------- production -------
FROM base

COPY --from=build /app /app

RUN chown -R www-data:www-data /app/var

ENV APP_ENV=prod
ENV APP_RUNTIME_ENV=prod

EXPOSE 8080
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

USER www-data
