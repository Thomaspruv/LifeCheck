// Global setup for Playwright E2E tests
const { spawnSync } = require('child_process');
const path = require('path');
const fs = require('fs');

const PROJECT_ROOT = path.resolve(__dirname, '..', '..');

function run(cmd, args, options) {
  const result = spawnSync(cmd, args, {
    cwd: PROJECT_ROOT,
    stdio: 'pipe',
    env: { ...process.env, ...options?.env },
    timeout: 60000,
    maxBuffer: 10 * 1024 * 1024,
  });

  if (result.status !== 0) {
    const stderr = result.stderr?.toString() || '';
    const stdout = result.stdout?.toString() || '';
    console.error(stdout);
    console.error(stderr);
    throw new Error(`Command failed: ${cmd} ${args.join(' ')}\n${stderr}`);
  }

  return result.stdout?.toString() || '';
}

async function globalSetup() {
  console.log('\n=== Playwright Global Setup: Seeding E2E test database ===\n');

  const dbPath = path.resolve(PROJECT_ROOT, 'database', 'e2e-testing.sqlite');
  console.log(`Database: ${dbPath}\n`);

  // Ensure database directory exists
  const dbDir = path.dirname(dbPath);
  if (!fs.existsSync(dbDir)) {
    fs.mkdirSync(dbDir, { recursive: true });
  }

  // Remove old database file to start fresh
  if (fs.existsSync(dbPath)) {
    fs.unlinkSync(dbPath);
    console.log('  Removed old database file');
  }

  // Create empty database file
  fs.writeFileSync(dbPath, '');

  const envVars = {
    APP_ENV: 'testing',
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: dbPath,
    APP_DEBUG: 'true',
    CACHE_STORE: 'array',
    SESSION_DRIVER: 'array',
  };

  console.log('  Running migrations...');
  run('php', ['artisan', 'migrate:fresh', '--quiet'], { env: envVars });

  console.log('  Seeding with E2E test data...');
  run('php', ['artisan', 'db:seed', '--class', 'E2ETestSeeder', '--quiet'], { env: envVars });

  console.log('  ✅ Database seeded successfully\n');
}

module.exports = globalSetup;
