FROM php:7.1-apache
MAINTAINER sinkcup <sinkcup@gmail.com>

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get install -y nasm nodejs
RUN apt-get upgrade -y
RUN apt-get install -y icu-devtools libicu-dev libssl-dev unzip zlib1g-dev
RUN docker-php-ext-install intl mbstring pdo_mysql zip
RUN apt-get clean \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN a2enmod rewrite headers
RUN touch /usr/local/etc/php/php.ini

WORKDIR /var/www/laravel

ADD composer.json /var/www/laravel/
ADD composer.lock /var/www/laravel/
RUN composer install --no-autoloader --no-scripts --no-dev

ADD package.json /var/www/laravel/
RUN npm install

COPY resources/js /var/www/laravel/resources/js
COPY resources/sass /var/www/laravel/resources/sass
COPY webpack.mix.js /var/www/laravel/
RUN npm run production

COPY app /var/www/laravel/app
COPY artisan /var/www/laravel/artisan
COPY bootstrap /var/www/laravel/bootstrap
COPY config /var/www/laravel/config
COPY database /var/www/laravel/database
COPY public /var/www/laravel/public
COPY resources /var/www/laravel/resources
COPY routes /var/www/laravel/routes
COPY server.php /var/www/laravel/server.php
COPY storage /var/www/laravel/storage
RUN composer install --no-dev \
    && chmod 777 bootstrap/cache \
    && chmod -R 777 storage/

RUN rm -f public/storage \
    && php artisan storage:link

RUN rm /etc/apache2/sites-enabled/*
ADD apache2/ /etc/apache2/
RUN sed -i 's/DocumentRoot [a-z\/\-]*\/public/DocumentRoot \/var\/www\/laravel\/public/g' `grep -lr DocumentRoot /etc/apache2/sites-enabled/`

COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

COPY . /var/www/laravel/
CMD ["/usr/local/bin/start"]
