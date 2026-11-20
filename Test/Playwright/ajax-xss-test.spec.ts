import {test, expect} from '@loki/test';
import {configure, getTestPageUrl, THEMES, waitForLokiComponents} from './helpers/configure';

declare const Alpine: any;

const HTML_ENDPOINT = '/loki_components/index/html';
const PAYLOAD = "'+(window.__lokiXss=1)+'\"</script>";

for (const theme of THEMES) {
    test.describe(`Loki Components request parameter escaping (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
        });

        test('does not execute script code injected via a query parameter', async ({page}) => {
            const response = await page.goto(getTestPageUrl() + '&zz=' + encodeURIComponent(PAYLOAD));
            expect(response?.status(), 'Test page response status').toBe(200);
            await waitForLokiComponents(page);

            const responsePromise = page.waitForResponse(response => {
                return response.url().includes(HTML_ENDPOINT) && response.request().method() === 'POST';
            });

            await page.evaluate(() => {
                const component = Alpine.store('LokiComponents').get('LokiComponentsTestDummy1');
                component.targets = ['loki-components-test-dummy2'];
                component.post('playwright-value');
            });

            const ajaxResponse = await responsePromise;
            expect(ajaxResponse.status()).toBe(200);
            expect(ajaxResponse.request().postDataJSON().request.params.zz).toBe(PAYLOAD);

            expect(await page.evaluate(() => (window as any).__lokiXss)).toBeUndefined();
        });
    });
}
