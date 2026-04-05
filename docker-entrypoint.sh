#!/bin/sh
set -e

# Substitute $PORT into the nginx config (Railway sets this dynamically)
export PORT="${PORT:-80}"
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Start php-fpm in the background, then nginx in the foreground
php-fpm -D
exec nginx -g "daemon off;"
