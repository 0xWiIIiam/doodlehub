# Use official PHP 8.2 with FPM (fast process manager) + Nginx
FROM php:8.2-fpm

# Install system deps + extensions for MySQL
RUN apt-get update && apt-get install -y \
    nginx \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy your PHP files into the container
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copy Nginx config (we'll make this next)
COPY nginx.conf /etc/nginx/sites-available/default

# Expose port 80
EXPOSE 80

# Start Nginx + PHP-FPM
CMD service nginx start && php-fpm