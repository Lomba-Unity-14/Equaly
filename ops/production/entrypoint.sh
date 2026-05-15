#!/bin/sh
set -e

echo "Running production setup..."

# Create required directories
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/app/public

# Set proper permissions (run as root)
chown -R www:www /var/www/html/storage
chown -R www:www /var/www/html/bootstrap/cache
chown -R www:www /var/www/html/database

# Create SQLite database if not exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch /var/www/html/database/database.sqlite
    chown www:www /var/www/html/database/database.sqlite
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force --ansi

# Run seeders
echo "Running seeders..."
php artisan db:seed --force --ansi

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link --ansi

# Publish Livewire assets for production
echo "Publishing Livewire assets..."
php artisan vendor:publish --tag=livewire:assets --force --ansi

# Cache configuration for production
echo "Caching configuration..."
php artisan config:cache --ansi
php artisan route:cache --ansi
php artisan view:cache --ansi

# Start supervisor (queue worker) as background process
echo "Starting queue worker..."
mkdir -p /var/log/supervisor
supervisord -c /etc/supervisor/conf.d/supervisord.conf

# Keep container running (supervisor manages both queue and php-fpm)
echo "Container ready. Waiting for supervisor..."
wait
