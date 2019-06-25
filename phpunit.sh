#!/usr/bin/env bash
STARTED_AT=$(date +%s)

php artisan migrate
if [ $? -ne 0 ]; then
    exit 1
fi

./vendor/bin/phpunit --stop-on-defect tests/
FLAG=$?

FINISHED_AT=$(date +%s)
echo 'Time taken: '$(($FINISHED_AT - $STARTED_AT))' seconds'
exit $FLAG
