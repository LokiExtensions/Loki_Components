import {test, expect} from '@loki/test';
import {configure, getTestPageUrl, THEMES} from './helpers/configure';

declare const LOKI_FORM_KEY: string;

const HTML_ENDPOINT = '/loki_components/index/html';

for (const theme of THEMES) {
    test.describe(`Loki Components form key cookie (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
        });

        test('keeps the rendered form key valid when form-key-provider.js loads late', async ({page}) => {
            let releaseProvider: () => void = () => {};
            const providerGate = new Promise<void>(resolve => releaseProvider = resolve);
            await page.route(/form-key-provider(\.min)?\.js/, async route => {
                await providerGate;
                await route.continue();
            });

            const response = await page.goto(getTestPageUrl(), {waitUntil: 'domcontentloaded'});
            expect(response?.status(), 'Test page response status').toBe(200);

            const renderedFormKey = await page.evaluate(() => LOKI_FORM_KEY);
            expect(renderedFormKey).toBeTruthy();

            const formKeyCookie = async () => (await page.context().cookies()).find(cookie => cookie.name === 'form_key');
            expect((await formKeyCookie())?.value).toBe(renderedFormKey);

            releaseProvider();
            await page.waitForLoadState('load');
            expect((await formKeyCookie())?.value).toBe(renderedFormKey);

            const body = await page.evaluate(async (endpoint) => {
                const response = await fetch(endpoint + '?form_key=' + LOKI_FORM_KEY + '&isAjax=true', {
                    method: 'POST',
                    mode: 'same-origin',
                    headers: {
                        'X-Alpine-Request': 'true',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({updates: [], targets: [], handles: ['default'], pageHandles: ['1column'], request: {}, signature: ''}),
                });

                return response.text();
            }, HTML_ENDPOINT);

            expect(body).not.toContain('Invalid Form Key');
        });
    });
}
