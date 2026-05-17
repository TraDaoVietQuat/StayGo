#!/bin/sh
php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan schedule:work >/tmp/scheduler.log 2>&1 &
exec php artisan serve --host=0.0.0.0 --port=$PORT
