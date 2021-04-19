FROM php:8.0-apache

WORKDIR /var/www/laravel

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - \
    && apt-get install -y \
    cron \
    icu-devtools \
    jq \
    libfreetype6-dev libicu-dev libjpeg62-turbo-dev libpng-dev libsasl2-dev libssl-dev libwebp-dev libxpm-dev libzip-dev \
    nodejs \
    unzip \
    zlib1g-dev \
    && apt-get clean \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && yes '' | pecl install redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install gd intl pdo_mysql zip \
    && docker-php-ext-enable opcache redis

COPY composer.json composer.lock ./
RUN composer install --no-autoloader --no-scripts --no-dev

COPY package.json package-lock.json /var/www/laravel/
RUN npm install

COPY docker/ /
RUN a2enmod rewrite headers \
    && a2ensite laravel \
    && a2dissite 000-default

COPY . /var/www/laravel
RUN composer install --optimize-autoloader --no-dev \
  && npm run production

RUN chown www-data:www-data bootstrap/cache \
    && chown -R www-data:www-data storage/ \
    && chmod +x /usr/local/bin/docker-laravel-entrypoint

CMD ["docker-laravel-entrypoint"]
