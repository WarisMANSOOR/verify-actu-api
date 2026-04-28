FROM php:8.2-apache

# J'installe les dépendances nécessaires pour Laravel et MySQL.
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# J'active le module rewrite d'Apache, nécessaire pour les routes Laravel.
RUN a2enmod rewrite

# J'ajoute Composer dans l'image Docker.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Je donne les droits nécessaires aux dossiers utilisés par Laravel.
RUN chown -R www-data:www-data storage bootstrap/cache

# Ici je configure Apache pour pointer directement vers le dossier public de Laravel.
# C'est important car Laravel démarre depuis public/index.php.
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]