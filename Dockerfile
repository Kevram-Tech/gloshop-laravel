# Production Dockerfile for Laravel GloShop
FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite expires ssl headers

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Copy package files for Node.js
COPY package.json package-lock.json* vite.config.js ./

# Install Node.js dependencies and build assets
RUN npm ci && npm run build && rm -rf node_modules && npm cache clean --force

# Copy application files
COPY . .

# Create necessary directories with proper permissions
RUN mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/app/public

# Copy Apache configurations
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
COPY docker/apache/security.conf /etc/apache2/conf-available/security.conf

# Enable security configuration
RUN a2enconf security

# Set proper ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy startup script
COPY docker/start-production.sh /usr/local/bin/start-production.sh
RUN chmod +x /usr/local/bin/start-production.sh

# Expose ports
EXPOSE 80 443

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Apache
CMD ["/usr/local/bin/start-production.sh"]

