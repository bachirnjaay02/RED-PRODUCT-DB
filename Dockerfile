# Utiliser l'image PHP officielle avec Apache
FROM php:8.2-apache

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les extensions PHP requises
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier les fichiers du projet
COPY . /var/www/html/

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurer Apache
RUN a2enmod rewrite
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Mettre à jour l'Apache configuration
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-enabled/000-default.conf

# Copier la configuration Apache
COPY .docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# Exposer le port
EXPOSE 80

# Générer la clé d'application
RUN cp .env.example .env && php artisan key:generate

# Exécuter les migrations et seeders au démarrage
RUN chmod +x /var/www/html/deploy.sh

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
  CMD curl -f http://localhost/ || exit 1

# Commande de démarrage
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan storage:link || true && apache2-foreground"]
