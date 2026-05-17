#!/bin/sh
echo "=== [START] $(date) ==="

echo "--- config:cache ---"
php artisan config:cache

echo "--- route:cache ---"
php artisan route:cache

echo "--- migrate ---"
php artisan migrate --force

echo "--- staygo:setup-demo-accounts ---"
php artisan staygo:setup-demo-accounts 2>&1
echo "--- setup done (exit: $?) ---"

echo "--- schedule:work (background) ---"
php artisan schedule:work >/tmp/scheduler.log 2>&1 &

echo "--- serve ---"
exec php artisan serve --host=0.0.0.0 --port=$PORT
