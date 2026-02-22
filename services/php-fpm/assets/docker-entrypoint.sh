#!/bin/sh

chown -Rf www-data:www-data /var/www/html
php-fpm --nodaemonize
php /var/www/html/ws.php 2>&1 &