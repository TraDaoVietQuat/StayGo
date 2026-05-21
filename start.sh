#!/bin/sh
php artisan config:cache
php artisan route:cache
php artisan filament:optimize
php artisan storage:link --quiet 2>/dev/null || true
php artisan migrate --force
php artisan schedule:work >/tmp/scheduler.log 2>&1 &
php artisan queue:work --sleep=3 --tries=3 --timeout=90 --max-time=3600 >/tmp/queue.log 2>&1 &
exec php artisan serve --host=0.0.0.0 --port=$PORT
