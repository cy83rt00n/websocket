FROM php:8.5-fpm-alpine

RUN apk add --update --no-cache linux-headers libpq-dev && \
    docker-php-ext-install -j4 sockets pdo pdo_pgsql pdo_mysql
COPY assets/docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 9000

WORKDIR /var/www/html

ENTRYPOINT [ "/docker-entrypoint.sh" ]