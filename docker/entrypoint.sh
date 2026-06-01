#!/usr/bin/env sh
set -e

cd /var/www/html

echo "Running Laravel startup tasks..."

required_vars="APP_KEY DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD"
for var in $required_vars; do
  val=$(printenv "$var" || true)
  if [ -z "$val" ]; then
    echo "ERROR: Required environment variable '$var' is missing."
    exit 1
  fi
done

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "Running migrations..."
  php artisan migrate --force
fi

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting supervisor (nginx + php-fpm + queue + scheduler)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
