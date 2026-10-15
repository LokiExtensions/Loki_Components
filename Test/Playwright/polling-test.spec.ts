import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

const POLLING_ENDPOINT = '/loki_components/index/polling';
const POLLING_INTERVAL_PATH = 'loki_components/general/polling_interval';

for (const theme of THEMES) {
    test.describe(`Loki Components polling (${theme})`, () => {
        test.afterEach(async ({page}) => {
            await configure(page, {theme, config: {[POLLING_INTERVAL_PATH]: 0}});
        });

        test('does not poll without a polling interval', async ({page}) => {
            await configure(page, {theme, config: {[POLLING_INTERVAL_PATH]: 0}});

            const pollingRequests: string[] = [];
            page.on('request', request => {
                if (request.url().includes(POLLING_ENDPOINT)) {
                    pollingRequests.push(request.url());
                }
            });

            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
            await page.waitForTimeout(1500);

            expect(pollingRequests).toEqual([]);
        });

        test('polls the polling endpoint with the configured interval', async ({page}) => {
            await configure(page, {theme, config: {[POLLING_INTERVAL_PATH]: 500}});

            const responsePromise = page.waitForResponse(response => response.url().includes(POLLING_ENDPOINT));
            await openTestPage(page, TEST_PAGE_HANDLES);

            const response = await responsePromise;
            expect(response.request().method()).toBe('GET');
            expect(response.url()).toContain('form_key=');
            expect(response.status()).toBe(200);
            expect(await response.json()).toEqual([]);
        });
    });
}
