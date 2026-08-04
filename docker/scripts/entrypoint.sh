#!/bin/sh
set -e

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

# The production public directory is a persistent volume shared with Nginx.
# Refresh it from the current image so new Vite manifests and bundles are served.
if [ -d /opt/app-public ]; then
    cp -a /opt/app-public/. public/
fi

if [ ! -L public/storage ]; then
    rm -rf public/storage
    ln -s ../storage/app/public public/storage
fi

php artisan package:discover --ansi

if [ "${APP_ENV:-production}" = "production" ]; then
    php artisan config:cache
    php artisan view:cache
fi

exec "$@"
