FROM nginx:stable-alpine

RUN rm -f /etc/nginx/conf.d/default.conf
COPY assets/etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

EXPOSE 80