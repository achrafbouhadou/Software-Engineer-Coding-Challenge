#!/bin/sh
set -e

echo "Setting up permissions..."
chmod -R 775 /var/www
chown -R www-data:www-data /var/www

echo "Clearing composer cache..."
composer clear-cache

echo "Running composer install..."
composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --no-progress

# Run post-install scripts separately
echo "Running post-install scripts..."
composer run-script post-install-cmd

# Check if APP_KEY is set
if ! grep -q '^APP_KEY=' .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

echo "Setting up storage link..."
php artisan storage:link

echo "Running database migrations..."
php artisan migrate --force

echo "Setting up Elasticsearch indices..."
php artisan elasticsearch:setup

# Handle custom commands
if [ "$#" -gt 0 ]; then
    exec "$@"
fi

echo "Starting Laravel application..."
exec php artisan serve --host=0.0.0.0 --port=9000