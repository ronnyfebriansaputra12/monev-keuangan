FROM php:8.2-apache

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 2. Aktifkan Apache Mod Rewrite (Biar Route Laravel Jalan)
RUN a2enmod rewrite

# 3. SETTINGAN BERSIH: Ubah Document Root ke /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Install Composer (Copy dari image resmi biar cepet)
COPY --from=composer:latest /usr/local/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html
COPY . .

# 5. Set Owner ke www-data (Agar tidak kena file_put_contents issue)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80