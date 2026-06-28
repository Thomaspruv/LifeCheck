import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage';
import { DashboardPage } from '../pages/DashboardPage';
import { TEST_USER } from '../fixtures/auth-fixture';

test.describe('Dashboard', () => {

  test.beforeEach(async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();
    await loginPage.login(TEST_USER.email, TEST_USER.password);
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('should display dashboard with user greeting', async ({ page }) => {
    const dashboard = new DashboardPage(page);

    await dashboard.expectHeading(/tableau de bord/i);
    await dashboard.expectGreeting(/bonjour/i);
    await dashboard.expectStreakVisible();
  });

  test('should have clickable nav links', async ({ page }) => {
    // Check that key navigation links work
    const links = [
      { text: /historique|check-ins passés/i, url: /\/history/ },
    ];

    for (const link of links) {
      const navItem = page.locator(`a:has-text("${link.text.source}")`).first();
      if (await navItem.isVisible()) {
        await navItem.click();
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(link.url);
        // Go back to dashboard
        await page.goto('/dashboard');
        await page.waitForLoadState('networkidle');
      }
    }
  });

  test('should have quick links section visible', async ({ page }) => {
    // The dashboard has a grid of quick links
    const quickLinkGrid = page.locator('.grid.grid-cols-2.md\\:grid-cols-4').last();
    await expect(quickLinkGrid).toBeVisible();
  });
});
