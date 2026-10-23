import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {Page} from '@playwright/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const LokiComponentsLogger: any;

const DEBUG_PATH = 'loki_components/general/debug';

function collectLoggerMessages(page: Page): string[] {
    const messages: string[] = [];
    page.on('console', message => {
        const text = message.text();
        if (text.includes('Loki_Components [') && text.includes('playwright-')) {
            messages.push(text);
        }
    });

    return messages;
}

for (const theme of THEMES) {
    test.describe(`Loki Components logger (${theme})`, () => {
        test.afterEach(async ({page}) => {
            await configure(page, {theme, config: {[DEBUG_PATH]: 0}});
        });

        test('logs prefixed messages per type', async ({page}) => {
            await configure(page, {theme, config: {[DEBUG_PATH]: 0}});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);

            const messages = collectLoggerMessages(page);
            await page.evaluate(() => {
                LokiComponentsLogger.notice('playwright-notice');
                LokiComponentsLogger.success('playwright-success');
                LokiComponentsLogger.warning('playwright-warning');
                LokiComponentsLogger.error('playwright-error');
            });

            await expect.poll(() => messages.length).toBe(4);
            expect(messages[0]).toContain('Loki_Components [NOTICE]');
            expect(messages[0]).toContain('playwright-notice');
            expect(messages[1]).toContain('Loki_Components [SUCCESS]');
            expect(messages[2]).toContain('Loki_Components [WARNING]');
            expect(messages[3]).toContain('Loki_Components [ERROR]');
        });

        test('does not log debug messages when debugging is disabled', async ({page}) => {
            await configure(page, {theme, config: {[DEBUG_PATH]: 0}});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);

            const messages = collectLoggerMessages(page);
            const allowDebugging = await page.evaluate(() => {
                LokiComponentsLogger.debug('playwright-debug');
                LokiComponentsLogger.notice('playwright-marker');
                return LokiComponentsLogger.allowDebugging;
            });

            expect(allowDebugging).toBe(false);
            await expect.poll(() => messages.length).toBe(1);
            expect(messages[0]).toContain('playwright-marker');
        });

        test('logs debug messages when debugging is enabled', async ({page}) => {
            await configure(page, {theme, config: {[DEBUG_PATH]: 1}});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);

            const messages = collectLoggerMessages(page);
            const allowDebugging = await page.evaluate(() => {
                LokiComponentsLogger.debug('playwright-debug');
                return LokiComponentsLogger.allowDebugging;
            });

            expect(allowDebugging).toBe(true);
            await expect.poll(() => messages.length).toBe(1);
            expect(messages[0]).toContain('Loki_Components [DEBUG]');
            expect(messages[0]).toContain('playwright-debug');
        });
    });
}
