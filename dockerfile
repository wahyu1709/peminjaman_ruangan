FROM php:8.4-apache

# Disable MPM event & enable prefork (paling stabil untuk Laravel)
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Install dependencies & extensions
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
    libicu-dev \
    libxslt1-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure GD
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

# Enable rewrite (untuk Laravel routing)
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Generate key
RUN php artisan key:generate --ansi || true

# Permission
RUN chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

# Expose port Railway
EXPOSE $PORT

# Start Apache
CMD apache2-foreground