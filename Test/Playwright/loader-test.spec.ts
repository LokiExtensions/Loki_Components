import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const Alpine: any;

const HTML_ENDPOINT = '/loki_components/index/html';
const SOURCE_ID = 'LokiComponentsTestDummy1';
const TARGET_ELEMENT = 'loki-components-test-dummy2';

for (const theme of THEMES) {
    test.describe(`Loki Components loader (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('marks the component and its targets as loading during a request', async ({page}) => {
            let releaseResponse: () => void = () => {};
            const responseReleased = new Promise<void>(resolve => releaseResponse = resolve);
            await page.route('**' + HTML_ENDPOINT + '**', async route => {
                await responseReleased;
                await route.continue();
            });

            const getState = () => page.evaluate((sourceId) => {
                const component = Alpine.store('LokiComponents').get(sourceId);
                return {loading: component.loading, showLoader: component.showLoader};
            }, SOURCE_ID);

            const requestPromise = page.waitForRequest(request => request.url().includes(HTML_ENDPOINT));
            await page.evaluate(({sourceId, targetElement}) => {
                const component = Alpine.store('LokiComponents').get(sourceId);
                component.targets = [targetElement];
                component.post('playwright-value');
            }, {sourceId: SOURCE_ID, targetElement: TARGET_ELEMENT});
            await requestPromise;

            expect((await getState()).loading).toBe(true);
            await expect(page.locator('#' + TARGET_ELEMENT)).toHaveAttribute('aria-busy', 'true');

            await expect.poll(async () => (await getState()).showLoader, {
                message: 'The loader is shown after the loader timeout',
            }).toBe(true);

            const responsePromise = page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));
            releaseResponse();
            await responsePromise;

            await expect.poll(getState).toEqual({loading: false, showLoader: false});
            await expect(page.locator('#' + TARGET_ELEMENT)).not.toHaveAttribute('aria-busy');
        });

        test('uses the default loader timeout', async ({page}) => {
            const timeout = await page.evaluate((sourceId) => {
                return Alpine.store('LokiComponents').get(sourceId).showLoaderTimeout;
            }, SOURCE_ID);

            expect(timeout).toBe(400);
        });
    });
}
