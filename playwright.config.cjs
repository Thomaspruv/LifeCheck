// @ts-check
const { defineConfig, devices } = require('@playwright/test');

const APP_URL = process.env.APP_URL || 'http://127.0.0.1:8000';
const CI = !!process.env.CI;

module.exports = defineConfig({
  globalSetup: require.resolve('./tests/e2e/global-setup.cjs'), // Reset DB automatiquement avant chaque run
  testDir: './tests/e2e',
  fullyParallel: false,
  forbidOnly: CI,
  retries: CI ? 1 : 0,
  workers: 1,
  reporter: [
    ['html', { outputFolder: 'playwright-report' }],
    ['list'],
  ],

  use: {
    baseURL: APP_URL,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    viewport: { width: 375, height: 667 },
    actionTimeout: 10000,
  },

  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
        viewport: { width: 375, height: 667 },
        launchOptions: {
          executablePath: '/opt/hermes/.playwright/chromium_headless_shell-1223/chrome-headless-shell-linux64/chrome-headless-shell',
        },
      },
    },
  ],

  // webServer: en développement, lancer le serveur manuellement :
  //   APP_ENV=testing DB_DATABASE=database/e2e-testing.sqlite php artisan serve
  // Réactiver cette section en CI uniquement.
  // webServer: {
  //   command: 'php artisan serve --port=8000',
  //   url: APP_URL,
  //   reuseExistingServer: true,
  //   timeout: 30000,
  //   env: {
  //     APP_ENV: 'testing',
  //     DB_CONNECTION: 'sqlite',
  //     DB_DATABASE: '/opt/data/LifeCheck/database/e2e-testing.sqlite',
  //   },
  // },
});
