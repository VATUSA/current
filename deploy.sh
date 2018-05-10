#!/bin/sh

source ./.config.sh

echo "Installing Node packages"
npm i
echo "Compiling scripts"
npm run production
echo "Done."

echo "Building container"
docker-compose build
docker tag vatusa/www vatusa/www:latest
echo "Done."

echo "Uploading to Docker Hub"
docker login -u "$DOCKER_USERNAME" -p "$DOCKER_PASSWORD"
docker push vatusa/www:latest
echo "Done"
