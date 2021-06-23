FROM vatusa/php-nginx

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

RUN mv /usr/local/etc/php/conf.d /etc/php7/conf.d/99-vatusa.ini

EXPOSE 80
