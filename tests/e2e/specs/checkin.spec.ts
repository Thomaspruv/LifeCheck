import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage';
import { DashboardPage } from '../pages/DashboardPage';
import { TEST_USER } from '../fixtures/auth-fixture';

test.describe('Check-in', () => {

  test.beforeEach(async ({ page }) => {
    // Login before each check-in test
    const loginPage = new LoginPage(page);
    await loginPage.goto();
    await loginPage.login(TEST_USER.email, TEST_USER.password);
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('should display check-in form with template items', async ({ page }) => {
    await page.goto('/checkin');
    await page.waitForLoadState('networkidle');

    // Should show template items
    await expect(page.locator('input[type="range"]')).toBeVisible();
    await expect(page.locator('textarea[placeholder*="ressens"]')).toBeVisible();

    // Should have submit button
    await expect(page.getByRole('button', { name: /valider/i })).toBeVisible();
  });

  test('should create a basic check-in (mood only)', async ({ page }) => {

    await page.goto('/checkin');
    await page.waitForLoadState('networkidle');

    // Click an emoji button for the first template item (Mood)
    await page.locator('button:has-text("😊")').first().click();
    // Small pause to let Alpine process the click and update the hidden input
    await page.waitForTimeout(100);

    // Fill the range slider for Sleep
    const slider = page.locator('input[type="range"]').first();
    await slider.fill('7');

    // Click the submit button (Valider mon check-in)
    await page.getByRole('button', { name: /valider/i }).click();
    await page.waitForURL(/\/dashboard/, { timeout: 15000 });

    // Should redirect to dashboard with success message
    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.locator('text=/✅.*[Cc]heck.in.*(enregistré|fait)/')).toBeVisible({ timeout: 5000 });
  });

  test('should create a full check-in with all fields', async ({ page }) => {

    await page.goto('/checkin?date=2020-01-01');
    await page.waitForLoadState('networkidle');

    // Click an emoji button for the first template item (Mood)
    await page.locator('button:has-text("😊")').first().click();
    await page.waitForTimeout(100);

    // Fill slider for Sleep
    const slider = page.locator('input[type="range"]').first();
    await slider.fill('8');

    // Fill textarea for the Notes template item
    const textarea = page.locator('textarea[placeholder*="ressens"]').first();
    await textarea.fill('Une très bonne journée !');

    // Write in the notes field
    const notes = page.locator('textarea[name="notes"]');
    await notes.fill('Journée productive et ensoleillée.');

    // Submit
    await page.getByRole('button', { name: /valider/i }).click();
    await page.waitForURL(/\/dashboard/, { timeout: 15000 });

    // Should redirect to dashboard
    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.locator('text=/✅.*[Cc]heck.in.*(enregistré|fait)/')).toBeVisible({ timeout: 5000 });
  });

  test('should show today check-in in history', async ({ page }) => {

    // First create a check-in
    await page.goto('/checkin?date=2020-01-02');
    await page.waitForLoadState('networkidle');
    // Click an emoji button for the first template item (Mood)
    await page.locator('button:has-text("😊")').first().click();
    await page.waitForTimeout(100);

    // Fill slider for Sleep
    const slider = page.locator('input[type="range"]').first();
    await slider.fill('7');

    await page.getByRole('button', { name: /valider/i }).click();
    await page.waitForURL(/\/dashboard/, { timeout: 15000 });

    // Go to history
    await page.goto('/history');
    await page.waitForLoadState('networkidle');

    // Should see the check-in entry in history
    await expect(page.locator('text=Thursday 02').first()).toBeVisible({ timeout: 5000 });
    await expect(page.locator('text=January 2020').first()).toBeVisible({ timeout: 5000 });
  });
});
