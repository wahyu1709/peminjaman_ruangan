FROM php:8.3-cli

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project
COPY . /app

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Expose port (Railway pakai PORT environment)
EXPOSE $PORT

# Jalankan Laravel
CMD php artisan serve --host=0.0.0.0 --port=$PORT