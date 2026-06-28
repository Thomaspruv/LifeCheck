import { Page } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage';

// Utilisateur de test commun
export const TEST_USER = {
  name: 'Test User',
  email: 'test@lifecheck.app',
  password: 'Password123!',
};

/**
 * Se connecter avec l'utilisateur de test.
 * Utilisé dans les hooks beforeEach des specs.
 */
export async function loginAsTestUser(page: Page) {
  const loginPage = new LoginPage(page);
  await loginPage.goto();
  await loginPage.login(TEST_USER.email, TEST_USER.password);
}

/**
 * Créer un utilisateur de test via le formulaire d'inscription.
 * Utile pour les tests d'auth.
 */
export async function registerTestUser(page: Page, options?: {
  name?: string;
  email?: string;
  password?: string;
}) {
  const user = { ...TEST_USER, ...options };
  await page.goto('/register');
  await page.waitForLoadState('networkidle');

  await page.locator('input[name="name"]').fill(user.name);
  await page.locator('input[name="email"]').fill(user.email);
  await page.locator('input[name="password"]').fill(user.password);
  await page.locator('input[name="password_confirmation"]').fill(user.password);
  await page.locator('button[type="submit"]').click();
  await page.waitForLoadState('networkidle');
}
