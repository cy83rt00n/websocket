#!/bin/sh

cp -Rf /code/. /var/www/html/;
chown -Rf 82:82 /var/www/html; 
tail -f /dev/null