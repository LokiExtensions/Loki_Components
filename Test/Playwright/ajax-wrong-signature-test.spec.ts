import { test, expect } from '@playwright/test';

const HTML_ENDPOINT = '/loki_components/index/html';

async function getFormKey(page): Promise<string> {
  await page.goto('/');

  const fromGlobal = await page.evaluate(() => {
    // @ts-ignore - injected by Loki Components
    return typeof LokiComponentFormKey !== 'undefined' ? LokiComponentFormKey : null;
  });
  if (fromGlobal) {
    return fromGlobal;
  }

  const cookies = await page.context().cookies();
  const formKeyCookie = cookies.find((cookie) => cookie.name === 'form_key');
  expect(formKeyCookie, 'A form_key cookie is required to build a valid AJAX request').toBeTruthy();

  return formKeyCookie!.value;
}

function buildRequest(page, formKey: string, body: object) {
  return page.request.post(HTML_ENDPOINT + '?form_key=' + encodeURIComponent(formKey) + '&isAjax=true', {
    headers: {
      'X-Alpine-Request': 'true',
      'X-Requested-With': 'XMLHttpRequest',
      'Content-Type': 'application/json',
    },
    data: body,
  });
}

test.describe('Loki Components AJAX endpoint signature validation', () => {
  test('rejects a well-formed request that carries a wrong signature', async ({ page }) => {
    const formKey = await getFormKey(page);

    const response = await buildRequest(page, formKey, {
      updates: [],
      targets: ['some-target'],
      handles: ['default'],
      pageHandles: ['1column'],
      request: {},
      signature: 'deadbeefdeadbeefdeadbeefdeadbeefdeadbeefdeadbeefdeadbeefdeadbeef',
    });

    expect(response.ok()).toBeFalsy();
    expect(response.status()).toBeGreaterThanOrEqual(400);

    const body = await response.text();
    expect(body).toContain('Payload was tampered with');
  });

  test('rejects a request whose handles were tampered with after signing', async ({ page }) => {
    const formKey = await getFormKey(page);

    const response = await buildRequest(page, formKey, {
      updates: [],
      targets: ['some-target'],
      handles: ['customer_account', 'customer_account_index'],
      pageHandles: ['1column'],
      request: {},
      signature: 'c0ffeec0ffeec0ffeec0ffeec0ffeec0ffeec0ffeec0ffeec0ffeec0ffeec0ff',
    });

    expect(response.ok()).toBeFalsy();
    expect(response.status()).toBeGreaterThanOrEqual(400);

    const body = await response.text();
    expect(body).toContain('Payload was tampered with');
  });
});
