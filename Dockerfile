# ============================================================
# Dockerfile – Co-Cooking API (Laravel 11)
# Image : PHP 8.3-FPM officielle + Nginx + Supervisor
# Plateforme : Render.com (Docker runtime)
# ============================================================

FROM php:8.4-fpm-alpine

# ---- Variables de build ----
ARG APP_ENV=production

# ---- Dépendances système ----
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        zip \
        gd \
        bcmath \
        opcache \
        intl \
    && rm -rf /var/cache/apk/*

# ---- Composer ----
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ---- Répertoire de travail ----
WORKDIR /var/www/html

# ---- Copier le code ----
COPY . .

# ---- Installer les dépendances PHP (sans dev) ----
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# ---- Permissions ----
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ---- Config Nginx ----
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# ---- Config PHP-FPM ----
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

# ---- Config Supervisor (gère Nginx + PHP-FPM) ----
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ---- Script de démarrage ----
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# ---- Port exposé ----
EXPOSE 80

CMD ["/start.sh"]