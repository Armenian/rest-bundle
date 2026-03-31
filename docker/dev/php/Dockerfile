#https://olvlvl.com/2019-06-docker-pecl-without-pecl
FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libpng-dev \
    libfreetype6-dev \
    libyaml-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libkrb5-dev \
    librabbitmq-dev \
    libxtst6 \
    p7zip \
    git \
    unzip \
    zip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

COPY ./docker/dev/php/docker-php-ext-get /usr/local/bin/
RUN chmod 0755 /usr/local/bin/docker-php-ext-get

# Extension versions
ENV PHP_IGBINARY_VERSION 3.2.16
ENV PHP_REDIS_VERSION 6.1.0
ENV PHP_AMQP_VERSION 2.2.0
ENV PHP_APCU_VERSION 5.1.24
ENV PHP_YAML_VERSION 2.3.0
ENV PHP_XDEBUG_VERSION 3.4.1
ENV PHP_ZIP_VERSION 1.22.5
ENV PHP_PCOV_VERSION 1.0.12

RUN docker-php-source extract \
    # ext-opcache
    && docker-php-ext-enable opcache \
    # ext-igbinary
    && docker-php-ext-get igbinary ${PHP_IGBINARY_VERSION} \
    && docker-php-ext-install igbinary \
    # ext-redis
    && docker-php-ext-get redis ${PHP_REDIS_VERSION} \
    && docker-php-ext-configure redis --enable-redis-igbinary \
    && docker-php-ext-install redis \
    # ext-apcu
    && docker-php-ext-get apcu ${PHP_APCU_VERSION} \
    && docker-php-ext-install apcu \
    # ext-yaml
    && docker-php-ext-get yaml ${PHP_YAML_VERSION} \
    && docker-php-ext-install yaml \
    # ext-amqp
    && docker-php-ext-get amqp ${PHP_AMQP_VERSION} \
    && docker-php-ext-install amqp \
    # ext-gd
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd \
    # ext-zip
    && docker-php-ext-get zip ${PHP_ZIP_VERSION} \
    && docker-php-ext-install zip \
    # ext-pcov
    && docker-php-ext-get pcov ${PHP_PCOV_VERSION} \
    && docker-php-ext-install pcov \
    # basic
    && docker-php-ext-install intl pdo pdo_pgsql pcntl \
    # ext-xdebug
    && docker-php-ext-get xdebug ${PHP_XDEBUG_VERSION} \
    && docker-php-ext-install xdebug \
    ## cleanup
    && docker-php-source delete

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
