import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const LokiComponentsStorage: any;

for (const theme of THEMES) {
    test.describe(`Loki Components local storage (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
            await page.evaluate(() => localStorage.removeItem('loki-components'));
        });

        test('returns an empty object when nothing is stored', async ({page}) => {
            const result = await page.evaluate(() => ({
                all: LokiComponentsStorage.get(),
                single: LokiComponentsStorage.load('foo'),
            }));

            expect(result.all).toEqual({});
            expect(result.single).toBeUndefined();
        });

        test('saves and loads values under the loki-components key', async ({page}) => {
            const result = await page.evaluate(() => {
                LokiComponentsStorage.save('foo', 'bar');
                LokiComponentsStorage.save('list', [1, 2]);
                LokiComponentsStorage.save('foo', 'baz');

                return {
                    foo: LokiComponentsStorage.load('foo'),
                    list: LokiComponentsStorage.load('list'),
                    raw: localStorage.getItem('loki-components'),
                };
            });

            expect(result.foo).toBe('baz');
            expect(result.list).toEqual([1, 2]);
            expect(JSON.parse(result.raw ?? '{}')).toEqual({foo: 'baz', list: [1, 2]});
        });

        test('keeps values after a reload', async ({page}) => {
            await page.evaluate(() => LokiComponentsStorage.save('persistent', {value: 42}));

            await page.reload();
            await waitForLokiComponents(page);

            expect(await page.evaluate(() => LokiComponentsStorage.load('persistent'))).toEqual({value: 42});
        });
    });
}
