import { Page, Locator, expect } from '@playwright/test';

export class CheckInPage {
  readonly page: Page;
  readonly moodSlider: Locator;
  readonly moodEmojis: Locator;
  readonly submitButton: Locator;
  readonly successMessage: Locator;
  readonly notesInput: Locator;
  readonly templateItems: Locator;

  constructor(page: Page) {
    this.page = page;
    this.moodSlider = page.locator('input[type="range"], input[name="mood"]');
    this.moodEmojis = page.locator('[data-testid="mood-emoji"]');
    this.submitButton = page.locator('button[type="submit"]');
    this.successMessage = page.locator('[data-testid="checkin-success"]');
    this.notesInput = page.locator('textarea[name="notes"]');
    this.templateItems = page.locator('[data-testid="template-item"]');
  }

  async goto() {
    await this.page.goto('/check-in/create');
    await this.page.waitForLoadState('networkidle');
  }

  async selectMood(value: number) {
    // Essayer d'abord le slider, puis les emojis
    if (await this.moodSlider.isVisible()) {
      await this.moodSlider.fill(String(value));
    } else {
      await this.moodEmojis.nth(value - 1).click();
    }
  }

  async fillNotes(notes: string) {
    if (await this.notesInput.isVisible()) {
      await this.notesInput.fill(notes);
    }
  }

  async submit() {
    await this.submitButton.click();
    await this.page.waitForLoadState('networkidle');
  }

  async expectSuccess() {
    await expect(this.successMessage).toBeVisible({ timeout: 10000 });
  }

  async catchUp(date: string) {
    // Mode catch-up : sélectionner une date passée
    await this.page.goto(`/check-in/catch-up?date=${date}`);
    await this.page.waitForLoadState('networkidle');
  }
}
