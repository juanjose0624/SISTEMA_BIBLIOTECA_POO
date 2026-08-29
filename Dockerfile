FROM php:8.2-apache

# Habilitar la extensión mysqli
RUN docker-php-ext-install mysqli

# Copiar el código del proyecto al directorio web de Apache
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80