#!/bin/sh
set -e

echo "Setting permissions..."
chmod -R 775 /var/www 
chown -R www-data:www-data /var/www

echo "Running composer install..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Check if APP_KEY is set; if not, generate one.
if ! grep -q '^APP_KEY=' .env; then
  echo "APP_KEY not found, generating one..."
  php artisan key:generate --force
fi

echo "Linking storage..."
php artisan storage:link

echo "Migrating the database..."
php artisan migrate --force

echo "Setting up Elasticsearch indices..."
php artisan elasticsearch:setup

# If any arguments are passed, run them instead of the default command.
if [ "$#" -gt 0 ]; then
  exec "$@"
fi

echo "Starting Laravel application..."
exec php artisan serve --host=0.0.0.0 --port=9000
