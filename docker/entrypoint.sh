#!/usr/bin/env sh
set -e

cd /var/www/html

echo "Running Laravel startup tasks..."

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ -n "$APP_KEY" ]; then
  echo "APP_KEY already set."
else
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "Running migrations..."
  php artisan migrate --force
fi

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting supervisor (nginx + php-fpm + queue + scheduler)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
