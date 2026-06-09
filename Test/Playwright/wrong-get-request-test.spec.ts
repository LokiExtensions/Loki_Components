import { test, expect } from '@playwright/test';

test('wrong get request test', async ({ page }) => {
  const response = await page.goto('/loki_components/index/html');
  await expect(response.status()).toBe(400);
});
