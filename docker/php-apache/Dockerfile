# Imagen base (elige versión según tu hosting)
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*
# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar GD correctamente
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd

# Activar mod_rewrite (Laravel)
RUN a2enmod rewrite

# Configurar zona horaria
ENV TZ=America/La_Paz

# Copiar configuración personalizada
COPY docker/php-apache/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php-apache/php.ini /usr/local/etc/php/

# Script de inicio (para permisos)
COPY docker/php-apache/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Exponer puerto
EXPOSE 80

# Ejecutar script
ENTRYPOINT ["/entrypoint.sh"]
