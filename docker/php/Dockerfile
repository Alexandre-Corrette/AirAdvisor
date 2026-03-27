FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
        libicu-dev \
        libzip-dev \
        libsqlite3-dev \
        libpq-dev \
        unzip \
        git \
    && docker-php-ext-install \
        intl \
        opcache \
        zip \
        pdo_sqlite \
        pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /app

EXPOSE 8000

CMD ["symfony", "serve", "--no-tls", "--port=8000", "--allow-all-ip"]
