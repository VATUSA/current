#!/bin/sh

# cat /run/secrets/key > /www/.env
# cat /run/secrets/*.env >> /www/.env

chown application:application /www/.env

# echo "*    *    *     *     *    cd /www && php artisan schedule:run" > /etc/crontabs/application

mkdir -p /www/storage/framework/views
mkdir -p /www/storage/framework/session
chown -R application:application /www/storage

if [[ "$ENV" == "prod" ]] || [[ "$ENV" == "livedev" ]] || [[ "$ENV" == "staging" ]]
then
  # echo "*    *    *     *     *    cd /www && php artisan schedule:run" >> /etc/crontabs/application
  cd /www && php artisan migrate
  chmod -R 775 /www/storage/logs
  chmod -R ugo+rw /www/storage
fi
