#!/bin/sh
set -eu

# The official image keeps /var/www/html as persistent runtime storage.
# Refresh application code on every container start while preserving files
# already present in the separately mounted uploads directory.
cp -a /usr/src/wordpress/. /var/www/html/
chown -R www-data:www-data /var/www/html/wp-content
chmod 640 /var/www/html/wp-config.php

exec /usr/local/bin/docker-entrypoint.sh "$@"
