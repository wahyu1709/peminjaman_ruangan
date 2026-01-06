# Gunakan PHP 8.2 CLI sebagai base image
FROM php:8.2-cli

# Install ekstensi PHP dan dependensi sistem yang dibutuhkan
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    libxslt1-dev \
    unzip \
    zip \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        soap \
        intl \
        xsl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy semua file ke dalam container
COPY . .

# Install dependencies PHP
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Generate key Laravel (opsional, kamu bisa set di Railway env)
RUN php artisan key:generate --ansi

# Beri izin pada folder storage & bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Jalankan server PHP built-in (untuk Railway)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]