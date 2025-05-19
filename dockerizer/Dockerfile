FROM webdevops/php-nginx:8.1-alpine

WORKDIR /app

COPY ./dockerizer/php.ini /opt/docker/etc/php/php.ini
COPY ./dockerizer/vhost.conf /opt/docker/etc/nginx/vhost.conf

COPY composer.json composer.lock /app/
RUN composer install --no-interaction --no-scripts

COPY . /app

# Ensure proper permissions
RUN mkdir -p /app/storage/logs /app/bootstrap/cache
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Run artisan command as the last step (after all setup)
USER root

# Install required PHP extensions for queue processing
RUN apk add --no-cache \
    bash \
    curl \
    git \
    zip \
    unzip \
    supervisor \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/cache/apk/*

# Set the runtime user back to www-data
USER www-data

# Command to run the queue listener
CMD ["php", "artisan", "queue:listen", "--verbose", "--tries=3", "--timeout=90"]