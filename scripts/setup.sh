#!/usr/bin/env bash
set -euo pipefail
echo "Running recommended local setup..."
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate || true
composer install
npm install
npm run build || echo "No frontend build configured"
echo "Setup complete."
