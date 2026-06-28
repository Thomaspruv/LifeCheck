import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage';
import { DashboardPage } from '../pages/DashboardPage';
import { TEST_USER } from '../fixtures/auth-fixture';

test.describe('Authentication', () => {

  test('should display login page with form fields', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();
    await loginPage.expectFormVisible();
  });

  test('should login with valid credentials', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboard = new DashboardPage(page);

    await loginPage.goto();
    await loginPage.login(TEST_USER.email, TEST_USER.password);

    // Should redirect to dashboard
    await dashboard.expectHeading(/tableau de bord/i);
    await dashboard.expectGreeting(/bonjour/i);
  });

  test('should show error with invalid credentials', async ({ page }) => {
    const loginPage = new LoginPage(page);

    await loginPage.goto();
    await loginPage.login('wrong@email.com', 'wrongpass');
    await expect(page.locator('.text-red-500, .text-red-600, [role="alert"]')).toBeVisible();
  });

  test('should redirect to dashboard when already logged in', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboard = new DashboardPage(page);

    // Login first
    await loginPage.goto();
    await loginPage.login(TEST_USER.email, TEST_USER.password);
    await dashboard.expectHeading(/tableau de bord/i);

    // Try to visit login page again
    await loginPage.goto();
    // Should be redirected to dashboard
    await dashboard.expectHeading(/tableau de bord/i);
  });

  test('should see streak and checkin button on dashboard', async ({ page }) => {
    const loginPage = new LoginPage(page);
    const dashboard = new DashboardPage(page);

    await loginPage.goto();
    await loginPage.login(TEST_USER.email, TEST_USER.password);
    await dashboard.expectHeading(/tableau de bord/i);
    await dashboard.expectStreakVisible();
    await dashboard.expectCheckinButtonVisible();
  });
});
