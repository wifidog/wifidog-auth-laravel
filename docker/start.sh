#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-production}

cd /var/www/laravel
php artisan migrate --force
rm -f public/storage
php artisan vendor:publish --all

if [ "$env" != "local" ]; then
    echo "Caching configuration..."
    php artisan config:cache
    php artisan view:cache
    php artisan route:cache
fi

if [[ "$role" = "app" ]]; then

    exec apache2-foreground

elif [[ "$role" = "scheduler" ]]; then

    echo "start cron"
    mkdir -p /var/spool/cron/crontabs/
    cp crontab /var/spool/cron/crontabs/root
    chmod 0644 /var/spool/cron/crontabs/root
    crontab /var/spool/cron/crontabs/root
    cron -f

elif [[ "$role" = "queue" ]]; then

    echo "Running the queue..."
    php artisan queue:work --queue={default} --verbose --tries=3 --timeout=90

else
    tail -f /var/log/faillog
fi
