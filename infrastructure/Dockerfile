# syntax=docker/dockerfile:1.3
FROM php:8.0-fpm-alpine

COPY --from=composer:2.1.5 /usr/bin/composer /usr/bin/composer

WORKDIR /opt/app
COPY composer.lock composer.json /opt/app/
RUN composer check-platform-reqs
RUN --mount=type=ssh composer install -o --no-scripts

COPY . /opt/app
ENV COMPOSER_ALLOW_XDEBUG=0
RUN --mount=type=ssh composer install -o
RUN composer qc
