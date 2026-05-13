#!/bin/sh
set -e

echo "Running production setup..."

# Set proper permissions
sudo chown -R www:www /var/www/html/storage
sudo chown -R www:www /var/www/html/bootstrap/cache
sudo chown -R www:www /var/www/html/database

# Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --ansi
fi

# Create SQLite database if not exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch /var/www/html/database/database.sqlite
    chown www:www /var/www/html/database/database.sqlite
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force --ansi

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link --ansi

# Cache configuration for production
echo "Caching configuration..."
php artisan config:cache --ansi
php artisan route:cache --ansi
php artisan view:cache --ansi

# Start supervisor (queue worker)
echo "Starting queue worker..."
supervisord -c /etc/supervisor/conf.d/supervisord.conf

# Execute the main command
exec "$@"
