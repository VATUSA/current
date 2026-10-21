# Built directly on the official PHP image rather than the vatusa/php-nginx chain
# (vatusa/dockerfile), which took PHP from Alpine's php83 packages and so could only
# ship a PHP security fix once Alpine repackaged it. The nginx/supervisor/entrypoint
# pieces of that chain now live in resources/docker. PHP2Shell, 2026-10-21.
FROM php:8.3.36-fpm-alpine AS app

ENV TIMEZONE=America/Chicago

RUN addgroup -S application && adduser -SG application application && \
    apk upgrade --no-cache && \
    apk add --no-cache ca-certificates curl tzdata nginx supervisor gmp && \
    cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
    echo "${TIMEZONE}" > /etc/timezone && \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Matches the extension set of the old Alpine image. igbinary/msgpack/sockets came
# along there only as package dependencies and nothing here uses them.
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS gmp-dev && \
    docker-php-ext-install -j"$(nproc)" pdo_mysql gmp && \
    pecl install redis-6.3.0 && \
    docker-php-ext-enable redis && \
    rm -rf /tmp/pear && \
    apk del .build-deps

COPY ./build.sh /entrypoint.d/50-build.sh

WORKDIR /www
COPY . /www
COPY ./resources/docker /
RUN rm -rf /www/resources/docker

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin && \
    rm composer-setup.php && \
    chown -R application:application /www

USER application
RUN composer.phar install --no-dev --no-scripts && mkdir -p /www/storage/framework/views
USER root
RUN rm /usr/local/bin/composer.phar

EXPOSE 80

CMD ["/bin/sh", "/usr/local/bin/start.sh"]
