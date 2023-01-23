FROM php:8.1.0-fpm
RUN set -xe \
   && apt-get update \
   && apt-get install -y libpng-dev procps libjpeg-dev libmcrypt-dev libpq-dev libonig-dev libzip-dev libxml2 libxml2-dev \
              curl \
              libcurl4-gnutls-dev cron \
              libmemcached11 \
              libmemcachedutil2 \
              libmemcached-dev \
              libz-dev \
   && rm -rf /var/lib/apt/lists/* \
   && docker-php-ext-configure gd \
   && docker-php-ext-configure intl \
   && docker-php-ext-install gd mbstring xml dom curl zip pdo pdo_pgsql intl\
   && pecl install mcrypt-1.0.5 \
   && docker-php-ext-enable mcrypt
WORKDIR /var/www/app/
