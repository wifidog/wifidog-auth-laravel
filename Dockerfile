FROM laravelfans/laravel:6

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y \
    nodejs \
    && apt-get clean \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY docker/etc /etc/

COPY package.json package-lock.json ./
RUN npm install

COPY composer.json composer.lock ./
RUN composer install --no-autoloader --no-scripts --no-dev

COPY . /var/www/laravel
RUN composer install --optimize-autoloader --no-dev \
  && npm run production
