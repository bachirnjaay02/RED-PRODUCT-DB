#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force

echo "Creating storage link..."
php artisan storage:link || true

echo "Clearing cache..."
php artisan cache:clear
php artisan config:clear

echo "Deployment completed successfully!"
