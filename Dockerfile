FROM wordpress:php8.3-apache

RUN a2enmod rewrite expires headers

COPY --chown=www-data:www-data . /var/www/html/

RUN find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod 640 /var/www/html/wp-config.php

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl --fail --silent http://127.0.0.1/wp-login.php > /dev/null || exit 1
