#!/bin/bash
set -e

composer install --no-interaction --no-progress

# Override DATABASE_URL with Docker-specific connection
export DATABASE_URL=postgresql://app:app_test@database:5432/app?serverVersion=16&charset=utf8

# Wait for database to be ready
until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
  echo "Waiting for database connection..."
  sleep 3
done

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures
php bin/console doctrine:fixtures:load --no-interaction

# Execute CMD
exec "$@"