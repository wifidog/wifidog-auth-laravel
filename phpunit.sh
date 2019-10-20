#!/usr/bin/env bash
set -e
STARTED_AT=$(date +%s)

php artisan migrate:fresh
php artisan migrate:refresh

./vendor/bin/phpunit --stop-on-defect --coverage-text tests/

FINISHED_AT=$(date +%s)
echo 'Time taken: '$((FINISHED_AT - STARTED_AT))' seconds'
