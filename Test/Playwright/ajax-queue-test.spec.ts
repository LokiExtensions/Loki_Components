import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {Page} from '@playwright/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const Alpine: any;

const HTML_ENDPOINT = '/loki_components/index/html';
const POLLING_ENDPOINT = '/loki_components/index/polling';
const POLLING_INTERVAL_PATH = 'loki_components/general/polling_interval';
const SOURCE_ID = 'LokiComponentsTestDummy1';
const TARGET_ELEMENT = 'loki-components-test-dummy2';

const isHtmlEndpointPost = (url: string, method: string) => url.includes(HTML_ENDPOINT) && method === 'POST';

async function postValue(page: Page, currentValue: any, value: any, skipQueue = false) {
    await page.evaluate(({sourceId, targetElement, currentValue, value, skipQueue}) => {
        const component = Alpine.store('LokiComponents').get(sourceId);
        component.targets = [targetElement];
        component.skipQueue = skipQueue;
        component.value = currentValue;
        component.post(value);
    }, {sourceId: SOURCE_ID, targetElement: TARGET_ELEMENT, currentValue, value, skipQueue});
}

for (const theme of THEMES) {
    test.describe(`Loki Components AJAX queue (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        for (const value of ['', 0, false]) {
            test(`posts the falsy value ${JSON.stringify(value)} instead of the current value`, async ({page}) => {
                const requestPromise = page.waitForRequest(request => isHtmlEndpointPost(request.url(), request.method()));

                await postValue(page, 'current-value', value);

                const body = (await requestPromise).postDataJSON();
                expect(body.updates[0].update).toEqual(value);
            });
        }

        test('posts the current value when no value is given', async ({page}) => {
            const requestPromise = page.waitForRequest(request => isHtmlEndpointPost(request.url(), request.method()));

            await postValue(page, 'current-value', undefined);

            const body = (await requestPromise).postDataJSON();
            expect(body.updates[0].update).toEqual('current-value');
        });

        test('prepares a component that skips the queue before posting', async ({page}) => {
            let releaseResponse: () => void = () => {};
            const responseReleased = new Promise<void>(resolve => releaseResponse = resolve);
            await page.route('**' + HTML_ENDPOINT + '**', async route => {
                await responseReleased;
                await route.continue();
            });

            const requestPromise = page.waitForRequest(request => isHtmlEndpointPost(request.url(), request.method()));
            await postValue(page, 'value-before-post', 'skip-queue-value', true);
            await requestPromise;

            const state = await page.evaluate((sourceId) => {
                const component = Alpine.store('LokiComponents').get(sourceId);
                return {loading: component.loading, beforePostValue: component.beforePostValue};
            }, SOURCE_ID);

            expect(state).toEqual({loading: true, beforePostValue: 'value-before-post'});

            releaseResponse();
            await expect.poll(() => page.evaluate((sourceId) => {
                return Alpine.store('LokiComponents').get(sourceId).loading;
            }, SOURCE_ID)).toBe(false);
        });

        test('sends only one request at a time', async ({page}) => {
            let inFlight = 0;
            let maxInFlight = 0;
            let requestCount = 0;
            await page.route('**' + HTML_ENDPOINT + '**', async route => {
                inFlight++;
                requestCount++;
                maxInFlight = Math.max(maxInFlight, inFlight);
                await new Promise(resolve => setTimeout(resolve, 500));
                await route.continue();
                inFlight--;
            });

            await postValue(page, 'current-value', 'first');
            await expect.poll(() => requestCount).toBe(1);
            await postValue(page, 'current-value', 'second');

            await expect.poll(() => requestCount, {timeout: 10000}).toBe(2);
            await expect.poll(() => inFlight, {timeout: 10000}).toBe(0);
            expect(maxInFlight).toBe(1);
        });

        test('does not leak variables into the global scope', async ({page}) => {
            const responsePromise = page.waitForResponse(response => isHtmlEndpointPost(response.url(), response.request().method()));
            await postValue(page, 'current-value', 'leak-test');
            await responsePromise;

            const leakedVariables = await page.evaluate(() => {
                return ['requests', 'key', 'value'].filter(name => Object.prototype.hasOwnProperty.call(window, name));
            });

            expect(leakedVariables).toEqual([]);
        });
    });

    test.describe(`Loki Components polling messages (${theme})`, () => {
        test.afterEach(async ({page}) => {
            await configure(page, {theme, config: {[POLLING_INTERVAL_PATH]: 0}});
        });

        test('shows messages of polling handlers that return keyed data', async ({page}) => {
            await configure(page, {theme, config: {[POLLING_INTERVAL_PATH]: 500}});

            await page.route('**' + POLLING_ENDPOINT + '**', route => route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify({stock: {messageType: 'warning', message: 'Low stock from Playwright'}}),
            }));

            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);

            await expect.poll(() => page.evaluate(() => Alpine.store('Message').messages))
                .toContainEqual({type: 'warning', text: 'Low stock from Playwright'});
        });

        test('shows messages of polling handlers that return a list', async ({page}) => {
            await configure(page, {theme, config: {[POLLING_INTERVAL_PATH]: 500}});

            await page.route('**' + POLLING_ENDPOINT + '**', route => route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify([{messageType: 'error', message: 'Out of stock from Playwright'}]),
            }));

            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);

            await expect.poll(() => page.evaluate(() => Alpine.store('Message').messages))
                .toContainEqual({type: 'error', text: 'Out of stock from Playwright'});
        });
    });
}
