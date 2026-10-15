import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {Page} from '@playwright/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const Alpine: any;

const HTML_ENDPOINT = '/loki_components/index/html';
const SOURCE_ID = 'LokiComponentsTestDummy1';
const SOURCE_BLOCK = 'loki-components.test.dummy1';
const TARGET_ID = 'LokiComponentsTestDummy2';
const TARGET_ELEMENT = 'loki-components-test-dummy2';

async function postUpdate(page: Page, value: string) {
    await page.evaluate(({sourceId, targetElement, value}) => {
        const component = Alpine.store('LokiComponents').get(sourceId);
        component.targets = [targetElement];
        component.post(value);
    }, {sourceId: SOURCE_ID, targetElement: TARGET_ELEMENT, value});
}

const isHtmlEndpointPost = (url: string, method: string) => url.includes(HTML_ENDPOINT) && method === 'POST';

for (const theme of THEMES) {
    test.describe(`Loki Components AJAX roundtrip (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('posts a signed update and replaces the target element', async ({page}) => {
            await page.evaluate((targetElement) => {
                document.getElementById(targetElement)?.setAttribute('data-playwright-marker', 'before-update');
            }, TARGET_ELEMENT);

            const requestPromise = page.waitForRequest(request => isHtmlEndpointPost(request.url(), request.method()));
            const responsePromise = page.waitForResponse(response => isHtmlEndpointPost(response.url(), response.request().method()));

            await postUpdate(page, 'playwright-value');

            const request = await requestPromise;
            expect(request.url()).toContain('form_key=');
            expect(request.headers()['x-alpine-request']).toBe('true');

            const body = request.postDataJSON();
            expect(body.updates).toEqual([{blockName: SOURCE_BLOCK, update: 'playwright-value'}]);
            expect(body.targets).toEqual([TARGET_ELEMENT]);
            expect(body.handles).toContain(TEST_PAGE_HANDLES[0]);
            expect(body.signature).toMatch(/^[a-f0-9]{64}$/);

            const response = await responsePromise;
            expect(response.status()).toBe(200);
            expect(response.headers()['content-type']).toContain('text/html');
            expect(response.headers()['x-magento-form-key']).toBeTruthy();
            expect(await response.text()).toContain('id="' + TARGET_ELEMENT + '"');

            const target = page.locator('#' + TARGET_ELEMENT);
            await expect(target, 'Target element is replaced by the AJAX response').not.toHaveAttribute('data-playwright-marker');
            await expect(target).toHaveAttribute('x-data', 'LokiComponent');
            await expect(target).toContainText('loki-components.test.dummy2');

            await expect.poll(() => page.evaluate((targetId) => {
                return Alpine.store('LokiComponents').getComponentIds().includes(targetId);
            }, TARGET_ID), {message: 'Target component is registered again'}).toBe(true);

            const errors = await page.evaluate(() => {
                return Alpine.store('Message').messages.filter((message: any) => message.type === 'error');
            });
            expect(errors, 'Error messages').toEqual([]);
        });

        test('shows the first line of a server error as global message', async ({page}) => {
            await page.route('**' + HTML_ENDPOINT + '**', route => route.fulfill({
                status: 500,
                contentType: 'text/html',
                body: 'Something went wrong on the server\nStack trace line',
            }));

            await postUpdate(page, 'playwright-value');

            await expect.poll(() => page.evaluate(() => Alpine.store('Message').messages))
                .toContainEqual({type: 'error', text: 'Something went wrong on the server'});
        });

        test('follows a JSON redirect', async ({page}) => {
            await page.route('**' + HTML_ENDPOINT + '**', route => route.fulfill({
                status: 200,
                contentType: 'application/json',
                headers: {'X-Loki-Redirect': '/customer/account/login/?playwright-redirect=1'},
                body: JSON.stringify({redirect: '/customer/account/login/?playwright-redirect=1'}),
            }));

            await postUpdate(page, 'playwright-value');

            await expect(page).toHaveURL(/playwright-redirect=1/);
        });

        test('shows messages that are transferred by the AJAX response', async ({page}) => {
            await page.route('**' + HTML_ENDPOINT + '**', route => route.fulfill({
                status: 200,
                contentType: 'text/html',
                body: '<html><body><script type="text/x-loki-messages">'
                    + JSON.stringify([{type: 'success', text: 'Saved by Playwright'}])
                    + '</script></body></html>',
            }));

            await postUpdate(page, 'playwright-value');

            await expect.poll(() => page.evaluate(() => Alpine.store('Message').messages))
                .toContainEqual({type: 'success', text: 'Saved by Playwright'});
        });
    });
}
