FROM php:8.2-fpm

# Install nginx and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends nginx libzip-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Copy nginx config template and startup script
COPY nginx.railway.conf /etc/nginx/nginx.conf.template
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Copy application code
COPY . /app

WORKDIR /app

EXPOSE 80

ENTRYPOINT ["/docker-entrypoint.sh"]
