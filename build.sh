#!/bin/sh

# cat /run/secrets/key > /www/.env
# cat /run/secrets/*.env >> /www/.env

chown application:application /www/.env

# echo "*    *    *     *     *    cd /www && php artisan schedule:run" > /etc/crontabs/application

mkdir /www/storage/framework/views
mkdir /www/storage/framework/session
chown -R application:application /www/storage

if [[ "$ENV" == "prod" ]] || [[ "$ENV" == "livedev" ]] || [[ "$ENV" == "staging" ]]; then
  # echo "*    *    *     *     *    cd /www && php artisan schedule:run" >> /etc/crontabs/application
  cd /www && php artisan migrate
  chmod -R 775 /www/storage/logs
fi
