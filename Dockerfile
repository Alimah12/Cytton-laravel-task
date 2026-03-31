FROM serversideup/php:8.3-fpm-nginx

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

USER root

RUN apt-get update \
	&& apt-get install -y --no-install-recommends libicu-dev \
	&& docker-php-ext-install intl \
	&& rm -rf /var/lib/apt/lists/*

USER www-data
RUN composer install --no-dev --optimize-autoloader --no-interaction

USER root
RUN chown -R www-data:www-data /var/www/html \
	&& chmod -R 775 storage bootstrap/cache

USER www-data
