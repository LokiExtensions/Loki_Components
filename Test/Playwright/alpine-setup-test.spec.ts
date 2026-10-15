import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, isHyva, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const Alpine: any;

for (const theme of THEMES) {
    test.describe(`Loki Components Alpine.js setup (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('loads Alpine.js exactly once', async ({page}) => {
            const lokiAlpineScript = page.locator('script[src*="Loki_Components/js/alpine"]');
            if (isHyva(theme)) {
                await expect(lokiAlpineScript, 'Hyvä ships its own Alpine.js').toHaveCount(0);
            } else {
                await expect(lokiAlpineScript).toHaveCount(1);
            }

            const alpineVersion = await page.evaluate(() => Alpine.version);
            expect(alpineVersion, 'Alpine.js version').toMatch(/^3\./);
        });

        test('registers the Loki stores and scripts', async ({page}) => {
            const registered = await page.evaluate(() => ({
                componentsStore: typeof Alpine.store('LokiComponents'),
                messageStore: typeof Alpine.store('Message'),
                // @ts-ignore - globals of Loki Components
                componentType: typeof LokiComponentType,
                // @ts-ignore
                ajaxQueue: typeof LokiAjaxQueue,
                // @ts-ignore
                htmlReplacer: typeof LokiHtmlReplacer,
                // @ts-ignore
                componentExtender: typeof LokiComponentExtender,
            }));

            expect(registered).toEqual({
                componentsStore: 'object',
                messageStore: 'object',
                componentType: 'object',
                ajaxQueue: 'object',
                htmlReplacer: 'object',
                componentExtender: 'object',
            });
        });
    });
}
