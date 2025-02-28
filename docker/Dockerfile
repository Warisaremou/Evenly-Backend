# Utiliser une image de base officielle de PHP
FROM php:8.4.1-fpm-alpine

# Set the working directory
WORKDIR /var/www

# Install system dependencies and build libraries
RUN apk update \
  && apk add --no-cache \
    curl \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    openssl-dev \
    libxml2-dev \
    curl-dev \
    icu-dev \
    libzip-dev \
  && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    opcache \
    intl \
    zip \
    bcmath \
    soap \
  # && pecl install redis \
  # && docker-php-ext-enable redis \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  && rm -rf /var/cache/apk/*

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Install composer dependencies
RUN composer install --no-scripts --no-autoloader

# Copy existing application directory contents
COPY . /var/www/

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
