#!/usr/bin/env sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

if [ ! -f vendor/autoload.php ]; then
    COMPOSER_PROCESS_TIMEOUT=0 composer install --no-interaction --prefer-dist --no-progress
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --no-interaction --force
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan filament:assets --ansi

exec "$@"
