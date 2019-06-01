FROM php:cli

RUN apt-get update

RUN apt-get install -y --no-install-recommends --fix-missing \
    git-core \
    gnupg \
    libfreetype6-dev \
    libicu-dev \
    librabbitmq-dev \
    libzip-dev \
    unzip \
    wget \
    zlib1g-dev \
    zip

RUN docker-php-ext-install -j$(nproc) \
    ctype \
    iconv \
    intl \
    opcache \
    pdo_mysql \
    zip

RUN pecl install \
    amqp \
    apcu \
    redis

RUN docker-php-ext-enable \
    amqp \
    apcu \
    redis

RUN mkdir -p /var/www/app

WORKDIR /var/www/app

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

ENTRYPOINT ["php", "-S", "0.0.0.0:8000", "-t", "public/"]
