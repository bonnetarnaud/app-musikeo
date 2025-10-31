# Base PHP-FPM
FROM php:8.2-fpm

# Installer dépendances PHP et outils système
RUN apt-get update && apt-get install -y \
        default-mysql-client \
        libonig-dev \
        libzip-dev \
        curl gnupg git unzip \
    && docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer Node.js pour Tailwind
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Installer Tailwind CSS, PostCSS et Autoprefixer globalement
RUN npm install -g tailwindcss postcss autoprefixer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet (optionnel si volume Docker utilisé)
# COPY . .

# Installer les dépendances JS locales (si package.json existe)
# RUN npm install

# Exposer le port PHP-FPM (Nginx se connectera ici)
EXPOSE 9000