#!/bin/bash
# Reset and seed the test database for E2E tests
# Usage: ./tests/e2e/setup.sh

set -e

echo "=== Setting up E2E test database ==="

# Make sure we're in the right dir
cd "$(dirname "$0")/../.."

# Set testing environment
export APP_ENV=testing
export DB_CONNECTION=sqlite
# Use a physical file instead of :memory: so the artisan serve process can see it
export DB_DATABASE=$(pwd)/database/e2e-testing.sqlite

echo "DB: $DB_DATABASE"

# Create the SQLite database file if it doesn't exist
touch "$DB_DATABASE"

# Migrate fresh with the e2e seeder
php artisan migrate:fresh --seed --seeder=Database\\Seeders\\E2ETestSeeder --env=testing --quiet 2>&1 || true

echo "=== E2E test database ready ==="
