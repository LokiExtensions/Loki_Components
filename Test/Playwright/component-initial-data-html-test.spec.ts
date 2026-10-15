import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES} from './helpers/configure';

declare const Alpine: any;

const BLOCK_NAME = 'loki-components.test.dummy-html';
const ELEMENT_ID = 'loki-components-test-dummy-html';
const HTML_PAYLOAD = '<div class="loki-components-test-injected">Store pickup</div></data><b class="loki-components-test-injected">Amsterdam</b>';

for (const theme of THEMES) {
    test.describe(`Loki Components initial data containing HTML (${theme})`, function () {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
        });

        test('keeps HTML in the initial data as data instead of markup', async function ({page}) {
            await openTestPage(page, TEST_PAGE_HANDLES);

            const component = page.locator('#' + ELEMENT_ID);
            await expect(component, 'Component element').toHaveAttribute('x-data', /.+/);

            await expect(page.locator('.loki-components-test-injected'), 'HTML from the initial data rendered as DOM').toHaveCount(0);

            await expect.poll(async () => {
                return page.evaluate((elementId) => {
                    const element = document.getElementById(elementId);
                    return element ? Alpine.$data(element).blockId : null;
                }, ELEMENT_ID);
            }, {message: 'blockId of the component'}).toBe(BLOCK_NAME);

            const html = await page.evaluate((elementId) => {
                return Alpine.$data(document.getElementById(elementId)).html;
            }, ELEMENT_ID);
            expect(html, 'HTML value in the component data').toBe(HTML_PAYLOAD);
        });
    });
}
