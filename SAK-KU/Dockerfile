# Menggunakan base image PHP
FROM php:8.2-cli

# Install dependencies sistem & driver PostgreSQL untuk Supabase
RUN apt-get update -y && apt-get install -y libpq-dev unzip
RUN docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Atur folder kerja di dalam server
WORKDIR /app

# Copy semua file project kamu ke dalam server
COPY . .

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Jalankan server aplikasi
CMD php artisan serve --host=0.0.0.0 --port=$PORT