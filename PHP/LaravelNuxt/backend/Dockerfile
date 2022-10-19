FROM php:8.1-fpm

RUN apt-get update \
    && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    mariadb-client \
    vim \
    zip \
    unzip \
    iputils-ping \
    git
RUN docker-php-ext-install zip pdo_mysql \
    && pecl install redis-5.3.7 \
    && docker-php-ext-enable redis

# composer
COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

RUN apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*x

COPY php.ini /etc/php/8.1/cli/conf.d/99-etc.ini

ADD docker-entrypoint.sh ./
RUN chmod +x ./docker-entrypoint.sh
CMD ["./docker-entrypoint.sh"]

WORKDIR /var/www/laravel
