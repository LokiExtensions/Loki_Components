import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const Alpine: any;

const HTML_ENDPOINT = '/loki_components/index/html';
const COMPONENT_ID = 'LokiComponentsTestToggle';
const BLOCK_ID = 'loki-components.test.toggle';
const ELEMENT_ID = 'loki-components-test-toggle';

for (const theme of THEMES) {
    test.describe(`Loki Components toggle component (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('renders the toggle component', async ({page}) => {
            const element = page.locator('#' + ELEMENT_ID);
            await expect(element).toHaveAttribute('x-data', 'LokiComponentToggle');
            await expect(element.locator('.loki-components-test-toggle-on')).toBeHidden();

            await expect.poll(() => page.evaluate(() => Alpine.store('LokiComponents').getComponentIds()))
                .toContain(COMPONENT_ID);
        });

        test('flips the value and posts it when toggled', async ({page}) => {
            let releaseResponse: () => void = () => {};
            const responseReleased = new Promise<void>(resolve => releaseResponse = resolve);
            await page.route('**' + HTML_ENDPOINT + '**', async route => {
                await responseReleased;
                await route.continue();
            });

            const requestPromise = page.waitForRequest(request => request.url().includes(HTML_ENDPOINT) && request.method() === 'POST');
            await page.locator('#' + ELEMENT_ID + ' .loki-components-test-toggle-button').click();

            const request = await requestPromise;
            expect(request.postDataJSON().updates).toEqual([{blockName: BLOCK_ID, update: true}]);

            const value = await page.evaluate((componentId) => Alpine.store('LokiComponents').get(componentId).value, COMPONENT_ID);
            expect(value).toBe(true);
            await expect(page.locator('#' + ELEMENT_ID + ' .loki-components-test-toggle-on')).toBeVisible();

            const responsePromise = page.waitForResponse(response => response.url().includes(HTML_ENDPOINT));
            releaseResponse();
            expect((await responsePromise).status()).toBe(200);
        });
    });
}
