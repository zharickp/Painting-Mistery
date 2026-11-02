# ─────────────────────────────────────────────────────────────────────
#  Painting Mistery — imagen de la aplicación (Laravel 12 + PHP 8.4)
#  Se arma en tres etapas para que la imagen final quede liviana:
#    1. vendor  → instala las librerías de PHP (Composer)
#    2. assets  → compila los estilos y el JS con Vite/Tailwind (Node)
#    3. final   → Apache + PHP con solo lo necesario para correr
#  La base de datos NO va aquí: el proyecto usa PostgreSQL en Neon.
# ─────────────────────────────────────────────────────────────────────

# 1) Librerías de PHP
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

# 2) Estilos y JS (public/build no se sube a GitHub, se genera aquí)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
# Tailwind también revisa las vistas de paginación que vienen en vendor
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build

# 3) Imagen final
FROM php:8.4-apache

# Extensiones de PHP para PostgreSQL + cliente de PostgreSQL 18
# (pg_dump y psql se usan para crear y restaurar las copias de seguridad;
#  deben ser de la misma versión que Neon o más nueva).
RUN apt-get update \
 && apt-get install -y --no-install-recommends ca-certificates curl gnupg libpq-dev libzip-dev unzip \
 && install -d /usr/share/postgresql-common/pgdg \
 && curl -fsSL -o /usr/share/postgresql-common/pgdg/apt.postgresql.org.asc https://www.postgresql.org/media/keys/ACCC4CF8.asc \
 && . /etc/os-release \
 && echo "deb [signed-by=/usr/share/postgresql-common/pgdg/apt.postgresql.org.asc] https://apt.postgresql.org/pub/repos/apt ${VERSION_CODENAME}-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
 && apt-get update \
 && apt-get install -y --no-install-recommends postgresql-client-18 \
 && docker-php-ext-install pdo_pgsql pgsql zip opcache \
 && apt-get purge -y --auto-remove gnupg \
 && rm -rf /var/lib/apt/lists/*

# Apache sirve la carpeta public/ y permite las URL de Laravel (.htaccess)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf \
 && a2enmod rewrite headers

# Configuración de PHP para producción. Las fotos permiten hasta 8 MB cada
# una y se pueden subir varias a la vez, por eso los límites.
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && { \
      echo 'upload_max_filesize=10M'; \
      echo 'post_max_size=60M'; \
      echo 'max_file_uploads=30'; \
      echo 'memory_limit=256M'; \
      echo 'date.timezone=America/Bogota'; \
      echo 'opcache.enable=1'; \
      echo 'opcache.validate_timestamps=0'; \
    } > "$PHP_INI_DIR/conf.d/painting-mistery.ini"

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

# Carpetas donde Laravel escribe (logs, caché, vistas, copias, fotos)
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
             storage/logs storage/app/backups bootstrap/cache \
             public/images/productos public/images/banners \
 && chown -R www-data:www-data storage bootstrap/cache public/images \
 && chmod -R ug+rwX storage bootstrap/cache public/images \
 && su www-data -s /bin/sh -c "php artisan package:discover --ansi"

EXPOSE 80

# Al arrancar: arma la caché de configuración/rutas/vistas con las variables
# del .env del servidor y luego inicia Apache.
CMD ["sh", "-c", "php artisan optimize && chown -R www-data:www-data storage bootstrap/cache && apache2-foreground"]
