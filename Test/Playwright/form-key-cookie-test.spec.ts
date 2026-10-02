import {test, expect} from '@playwright/test';

declare const LOKI_FORM_KEY: string;

const HTML_ENDPOINT = '/loki_components/index/html';
const TEST_PAGE = '/loki_components/index/test';

test.describe('Loki Components form key cookie', () => {
    test('keeps the rendered form key valid when form-key-provider.js loads late', async ({page}) => {
        let releaseProvider: () => void = () => {};
        const providerGate = new Promise<void>(resolve => releaseProvider = resolve);
        await page.route(/form-key-provider(\.min)?\.js/, async route => {
            await providerGate;
            await route.continue();
        });

        await page.goto(TEST_PAGE, {waitUntil: 'domcontentloaded'});

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
