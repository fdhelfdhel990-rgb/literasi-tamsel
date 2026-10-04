# Render Docker deployment: a single Laravel + Blade application.
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

FROM php:8.2-apache
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates git unzip libpq-dev libzip-dev libonig-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip mbstring \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --optimize-autoloader
COPY . .
COPY --from=frontend /app/public/build ./public/build
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache \
    && test -f public/health.txt \
    && test "$(cat public/health.txt)" = "OK" \
    && chown -R www-data:www-data storage bootstrap/cache \
    && rm -f bootstrap/cache/config.php bootstrap/cache/routes-*.php bootstrap/cache/events.php \
    && composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && printf '<Directory /var/www/html/public>\nOptions -Indexes +FollowSymLinks\nAllowOverride All\nRequire all granted\nFallbackResource /index.php\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel
EXPOSE 80
RUN chmod +x docker/entrypoint.sh
ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["apache2-foreground"]
