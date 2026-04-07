FROM php:8.2-apache

# Instalar soporte para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Activar mod_rewrite
RUN a2enmod rewrite
