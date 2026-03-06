#!/bin/bash
set -e

# Default to port 10000 if PORT not set (Render default)
export PORT=${PORT:-10000}

echo "==> Configuring Nginx to listen on port $PORT..."
envsubst '$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.tmp
mv /etc/nginx/conf.d/default.conf.tmp /etc/nginx/conf.d/default.conf

# Verify nginx config is valid
echo "==> Testing Nginx configuration..."
nginx -t

echo "==> Caching Laravel config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running database migrations..."
php artisan migrate --force || echo "WARNING: Migrations failed, continuing startup..."

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> Starting Supervisor (PHP-FPM + Nginx) on port $PORT..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
