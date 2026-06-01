### Multi-stage Dockerfile
# Stage 1: build frontend assets with PHP base
FROM php:8.4-cli AS node-builder
WORKDIR /var/www/html

# Install Node.js 22 and pnpm
RUN apt-get update \
    && apt-get install -y curl gnupg ca-certificates build-essential \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g pnpm@latest \
    && rm -rf /var/lib/apt/lists/*

COPY . .

# Install PHP deps and build assets
RUN composer install --no-dev --no-interaction --prefer-dist || true
RUN pnpm install --frozen-lockfile --unsafe-perm=true || true
RUN pnpm approve-builds --all || true
RUN pnpm build

# Stage 2: PHP-FPM + Nginx + Supervisor (all-in-one for simplicity)
FROM php:8.4-fpm-alpine
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    bash \
    git \
    icu-dev \
    libzip-dev \
    zlib-dev \
    oniguruma-dev \
    unzip \
    libpng-dev \
    libxml2-dev \
    && docker-php-ext-install -j$(nproc) \
        intl \
        pdo \
        pdo_mysql \
        zip \
        mbstring \
        bcmath \
        opcache \
        gd \
        xml

WORKDIR /var/www/html

# Copy built frontend
COPY --from=node-builder /var/www/html/public/build public/build

# Copy application
COPY . .

# Install composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy configs
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
