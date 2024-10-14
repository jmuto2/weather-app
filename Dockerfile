FROM php:8.3-fpm-alpine3.20 AS base

ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US:en
ENV LC_ALL=en_US.UTF-8

RUN apk update && apk add --no-cache curl make git bash nginx apk-cron \
    autoconf linux-headers && rm -rf /var/cache/apk/*
RUN docker-php-ext-install bcmath sockets
RUN mkdir -p /run/nginx
RUN mkdir -p /run/php
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/startup.sh /usr/local/bin/startup.sh
COPY docker/crontab /etc/crontab
RUN /usr/bin/crontab /etc/crontab
WORKDIR /app
EXPOSE 80

FROM base AS development

FROM base AS production
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN docker-php-ext-install opcache
COPY . /app
RUN chown -R www-data: /app
CMD ["/usr/local/bin/startup.sh"]
