#!/bin/bash

# Default to port 10000 if PORT not set (Render default)
export PORT=${PORT:-10000}

echo "==> Configuring Nginx to listen on port $PORT..."
envsubst '$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.tmp
mv /etc/nginx/conf.d/default.conf.tmp /etc/nginx/conf.d/default.conf

# Verify nginx config is valid
echo "==> Testing Nginx configuration..."
nginx -t || echo "WARNING: Nginx config test failed, check logs"

# Show the final nginx config for debugging
echo "==> Final Nginx server block:"
head -5 /etc/nginx/conf.d/default.conf

# START SUPERVISOR FIRST so Nginx opens the port immediately
# Render needs to detect an open port quickly or the deploy fails
echo "==> Starting Supervisor (PHP-FPM + Nginx) on port $PORT..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &
SUPERVISOR_PID=$!

# Give Nginx a moment to bind the port
sleep 2

echo "==> Caching Laravel config, routes, views..."
php artisan config:cache || echo "WARNING: config:cache failed, continuing..."
php artisan route:cache || echo "WARNING: route:cache failed, continuing..."
php artisan view:cache || echo "WARNING: view:cache failed, continuing..."

echo "==> Running database migrations..."
php artisan migrate --force || echo "WARNING: Migrations failed, continuing startup..."

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> Setup complete! Waiting on Supervisor..."
wait $SUPERVISOR_PID
