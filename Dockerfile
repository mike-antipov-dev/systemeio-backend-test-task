FROM php:8.3-cli-alpine AS sio_test
RUN apk add --no-cache git zip bash

# Install Xdebug
RUN apk add --no-cache --update linux-headers $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Setup PHP extensions (PostgreSQL)
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql

ENV COMPOSER_CACHE_DIR=/tmp/composer-cache
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup PHP app user
ARG USER_ID=1000
RUN adduser -u ${USER_ID} -D -H app
USER app

COPY --chown=app . /app
WORKDIR /app

COPY entrypoint.sh /app/
ENTRYPOINT ["/app/entrypoint.sh"]

EXPOSE 8337
EXPOSE 9003

CMD ["php", "-S", "0.0.0.0:8337", "-t", "public"]