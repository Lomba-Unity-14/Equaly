# Stage 1: Build Assets
FROM node:20-alpine AS builder

WORKDIR /var/www/html

# Copy package files
COPY package*.json ./
RUN npm install --ignore-scripts

# Copy source code and build
COPY . .
RUN npm run build

# Stage 2: Production Runtime
FROM php:8.4-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev \
    supervisor \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-install \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure OPcache for production
RUN { \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /var/www/html

# Create system user
RUN addgroup -g 1000 www \
    && adduser -u 1000 -G www -s /bin/sh -D www

# Copy application code
COPY --chown=www:www . /var/www/html
COPY --chown=www:www --from=builder /var/www/html/public/build /var/www/html/public/build

# Backup built assets (bind mount of public/ will override them at runtime)
RUN cp -r /var/www/html/public/build /tmp/public-build-backup

# Copy composer files and install dependencies
COPY composer*.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy supervisor configuration
COPY ops/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy PHP-FPM pool configuration
COPY ops/production/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copy entrypoint script
COPY ops/production/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permissions
RUN chown -R www:www /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/database

# Expose port 9000
EXPOSE 9000

# Entrypoint (runs as root to set permissions, then switches to www user)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
