FROM php:7.2-fpm-alpine

USER root

RUN apk update
RUN apk add --no-cache git supervisor && mkdir -p /var/log/supervisor
RUN docker-php-ext-install bcmath

COPY ./ /var/www/analytics/scraper

COPY php.ini /usr/local/etc/php
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD supervisord -c /etc/supervisor/conf.d/supervisord.conf -j /var/www/analytics/scraper/storage/supervisor/supervisor.pid -l /var/www/analytics/scraper/storage/logs/supervisor.log