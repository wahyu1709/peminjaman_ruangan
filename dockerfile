FROM php:8.3-apache

# Update & install dependencies sistem + library ICU yang benar
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
    unzip \
    zip \
    libicu-dev \           # <-- INI YANG BENAR untuk intl
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure & install GD dengan support lengkap
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    soap \
    xsl

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory ke public Laravel
WORKDIR /var/www/html

# Copy semua file proyek
COPY . .

# Install dependencies Composer (no dev untuk production)
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Generate key (jika belum ada di env)
RUN php artisan key:generate --ansi || true

# Set permission storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache 
    && chmod -R 775 storage bootstrap/cache

# Expose port (Railway pakai $PORT)
EXPOSE $PORT

# Command start Apache
CMD apache2-foreground