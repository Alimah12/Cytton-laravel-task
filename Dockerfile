FROM serversideup/php:8.3-fpm-nginx

# Set working directory
WORKDIR /var/www/html

# Copy project files

COPY --chown=www-data:www-data . .

# Install intl extension (required by PHP internationalization functions)
# and cleanup apt lists to keep the image small
RUN apt-get update \
	&& apt-get install -y libicu-dev \
	&& docker-php-ext-install intl \
	&& rm -rf /var/lib/apt/lists/*

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Laravel
RUN chmod -R 775 storage bootstrap/cache
