# syntax=docker/dockerfile:1.3
FROM php:8.0-fpm-alpine

ARG COMPOSER_VERSION=2.1.3

# Add runtime dependencies to the env variable
ENV RUNTIME_DEPS="icu-dev bash libzip-dev gnu-libiconv openssh-client"
RUN apk add --update --no-cache $RUNTIME_DEPS $PHPIZE_DEPS
RUN docker-php-ext-install zip intl
RUN apk del $PHPIZE_DEPS

# Install composer
ENV COMPOSER_DEPS zip unzip wget
RUN apk add --update --no-cache git $COMPOSER_DEPS \
    && curl -sS https://getcomposer.org/installer > /tmp/composer-setup.php \
    && COMPOSER_INST_SHA="$(curl -s https://composer.github.io/installer.sig)" \
    && php -r "if (hash_file('sha384', '/tmp/composer-setup.php') != '$COMPOSER_INST_SHA') { exit(1); }" \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer --version=$COMPOSER_VERSION \
    && composer --version \
    && apk del --purge $COMPOSER_DEPS

WORKDIR /opt/app
COPY composer.lock composer.json /opt/app/
RUN composer check-platform-reqs
RUN --mount=type=ssh composer install -o --no-scripts

COPY . /opt/app
ENV COMPOSER_ALLOW_XDEBUG=0
RUN --mount=type=ssh composer install -o
RUN composer qc
