import { test, expect } from '@playwright/test';

const HTML_ENDPOINT = '/loki_components/index/html';

test.describe('Loki Components AJAX endpoint bad requests', () => {
  test('rejects a GET request with a 400 status', async ({ page }) => {
    const response = await page.goto(HTML_ENDPOINT);
    expect(response).not.toBeNull();
    expect(response!.status()).toBe(400);
  });

  test('rejects a POST without a valid form key with a 400 status', async ({ request }) => {
    const response = await request.post(HTML_ENDPOINT + '?isAjax=true', {
      headers: {
        'X-Alpine-Request': 'true',
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
      },
      data: {
        updates: [],
        targets: [],
        handles: ['default'],
        pageHandles: ['1column'],
        request: {},
        signature: '',
      },
    });

    expect(response.status()).toBe(400);

    const body = await response.text();
    expect(body).toContain('Invalid Form Key');
  });
});
