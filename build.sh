#!/bin/sh

cat /run/secrets/key > /www/.env
cat /run/secrets/www.env >> /www/.env
cat /run/secrets/*.env >> /www/.env

chown application:application /www/.env

echo "*    *    *     *     *    cd /www && php artisan schedule:run" > /etc/crontabs/application

mkdir /www/storage/framework/views
chown application:application /www/storage/framework/views
