FROM richarvey/php-apache-heroku:latest
COPY . /var/www/html
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
RUN composer install --no-dev