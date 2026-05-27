#!/bin/bash

if [ ! -d "vendor" ]; then
    echo "Instalando dependencias Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
else
    echo "Dependencias ya instaladas, omitiendo Composer"
fi

echo "Creando estructura de storage..."

# Carpetas base
mkdir -p storage/app/public
mkdir -p storage/app/private

# Subcarpetas PUBLIC
mkdir -p storage/app/public/convocatorias
mkdir -p storage/app/public/galeria_sedes
mkdir -p storage/app/public/imagenes_convocatorias
mkdir -p storage/app/public/imagenes_noticias
mkdir -p storage/app/public/mallas_curriculares
mkdir -p storage/app/public/resoluciones

# Subcarpetas PRIVATE
mkdir -p storage/app/private/contratos
mkdir -p storage/app/private/documentos_infraestructura
mkdir -p storage/app/private/planos
mkdir -p storage/app/private/documentos_estudiantes
mkdir -p storage/app/private/documentos_estudiantes/certificado_habilitacion
mkdir -p storage/app/private/documentos_estudiantes/fotocopia_titulo
mkdir -p storage/app/private/documentos_estudiantes/requisitos
mkdir -p storage/app/private/documentos_estudiantes/copia_titulo
mkdir -p storage/app/private/documentos_estudiantes/formularios


echo "Configurando permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Creando storage link..."
php artisan storage:link || true

echo "Iniciando Apache..."
apache2-foreground