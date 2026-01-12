FROM php:8.3-fpm

# 1. Install system dependencies
# Bagian ini harus rapi formatnya
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    curl \
    wget \
    git \
    vim \
    unzip \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# 2. Konfigurasi GD (Agar support JPEG & Freetype)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# 3. Install PHP extensions (Disatukan agar build lebih cepat)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
# Catatan: Jika ini environment lokal, sebaiknya composer install dijalankan manual 
# atau pastikan folder vendor masuk .dockerignore
RUN composer install --optimize-autoloader --no-dev --prefer-dist
RUN composer install --optimize-autoloader --no-dev --prefer-dist --no-scripts
# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]