#!/bin/bash

if [ ! -d "vendor" ]; then
    echo "Instalando dependencias Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
else
    echo "Dependencias ya instaladas, omitiendo Composer"
fi

echo "Configurando permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Creando storage link..."
php artisan storage:link || true

echo "Iniciando Apache..."
apache2-foreground