FROM php:8.2-apache

# Install the PDO MySQL extension so PHP can talk to MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copy the application into Apache's web root
COPY index.php /var/www/html/index.php

EXPOSE 80
