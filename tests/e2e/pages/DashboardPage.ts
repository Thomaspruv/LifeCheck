import { Page, Locator, expect } from '@playwright/test';

export class DashboardPage {
  readonly page: Page;
  readonly heading: Locator;    // h2 "Tableau de bord"
  readonly greeting: Locator;   // h3 "Bonjour, {name} !"
  readonly streakCount: Locator;
  readonly checkinButton: Locator;
  readonly catchupPrompt: Locator;
  readonly navLinks: Locator;
  readonly userMenu: Locator;

  constructor(page: Page) {
    this.page = page;
    this.heading = page.getByRole('heading', { level: 2 });
    this.greeting = page.getByRole('heading', { level: 3 }).first();
    this.streakCount = page.locator('p.text-3xl.font-bold.text-gray-900').first();
    this.checkinButton = page.locator('a[href*="check-in/create"], a:has-text("Faire mon check-in")');
    this.catchupPrompt = page.locator('a[href*="check-in/catch-up"], a:has-text("Rattraper")');
    this.userMenu = page.locator('[data-testid="user-menu"]');
  }

  async goto() {
    await this.page.goto('/dashboard');
    await this.page.waitForLoadState('networkidle');
  }

  async expectHeading(text: string | RegExp) {
    await expect(this.heading).toContainText(text);
  }

  async expectGreeting(text: string | RegExp) {
    await expect(this.greeting).toContainText(text);
  }

  async expectStreakVisible() {
    await expect(this.streakCount).toBeVisible();
  }

  async expectCheckinButtonVisible() {
    await expect(this.checkinButton).toBeVisible();
  }

  async expectCheckinButtonHidden() {
    await expect(this.checkinButton).not.toBeVisible();
  }
}
