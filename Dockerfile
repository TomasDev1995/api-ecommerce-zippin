# Usar una imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instalar extensiones necesarias de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Configurar el working directory
WORKDIR /var/www/html

# Copiar el contenido del proyecto Laravel a la imagen
COPY . /var/www/html

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias de Composer
RUN composer install

# Exponer puertos para Apache
EXPOSE 80

# Crear archivo de configuración para el Virtual Host
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    ServerName ecommerce.zippin.com' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /var/www/html/public' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    ErrorLog /var/log/apache2/error.log' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    CustomLog /var/log/apache2/access.log combined' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Habilitar el módulo rewrite de Apache
RUN a2enmod rewrite


