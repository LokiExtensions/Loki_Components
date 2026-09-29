import {test, expect} from '@playwright/test';

declare const Alpine: any;

const TEST_PAGE = '/loki_components/index/test';
const BLOCK_NAME = 'loki-components.test.dummy-html';
const ELEMENT_ID = 'loki-components-test-dummy-html';
const HTML_PAYLOAD = '<div class="loki-components-test-injected">Store pickup</div></data><b class="loki-components-test-injected">Amsterdam</b>';

test.describe('Loki Components initial data containing HTML', function () {
    test('keeps HTML in the initial data as data instead of markup', async function ({page}) {
        const pageErrors: string[] = [];
        page.on('pageerror', (error) => pageErrors.push(error.message));

        await page.goto(TEST_PAGE);

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

        expect(pageErrors, 'Page errors').toEqual([]);
    });
});
