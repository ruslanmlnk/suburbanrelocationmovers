FROM wordpress:php8.3-apache

COPY docker/apache/servername.conf /etc/apache2/conf-available/servername.conf

RUN a2enmod rewrite expires headers \
    && a2enconf servername

COPY --chown=www-data:www-data . /usr/src/wordpress/
COPY docker/wordpress/site-entrypoint.sh /usr/local/bin/site-entrypoint.sh

RUN find /usr/src/wordpress -type d -exec chmod 755 {} \; \
    && find /usr/src/wordpress -type f -exec chmod 644 {} \; \
    && chmod 640 /usr/src/wordpress/wp-config.php \
    && sed -i 's/\r$//' /usr/local/bin/site-entrypoint.sh \
    && chmod 755 /usr/local/bin/site-entrypoint.sh

ENTRYPOINT ["site-entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl --fail --silent http://127.0.0.1/wp-includes/images/blank.gif > /dev/null || exit 1
