import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const Alpine: any;

const COMPONENTS = [
    {id: 'LokiComponentsTestDummy1', blockId: 'loki-components.test.dummy1', elementId: 'loki-components-test-dummy1'},
    {id: 'LokiComponentsTestDummy2', blockId: 'loki-components.test.dummy2', elementId: 'loki-components-test-dummy2'},
    {id: 'LokiComponentsTestDummyHtml', blockId: 'loki-components.test.dummy-html', elementId: 'loki-components-test-dummy-html'},
];

for (const theme of THEMES) {
    test.describe(`Loki Components store (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        for (const component of COMPONENTS) {
            test(`renders and registers component ${component.blockId}`, async ({page}) => {
                const element = page.locator('#' + component.elementId);
                await expect(element).toHaveAttribute('x-data', 'LokiComponent');
                await expect(element).toContainText(component.blockId);

                const initialData = element.locator(':scope > data[x-ref="initialData"]');
                await expect(initialData).toHaveCount(1);
                await expect(initialData).toBeHidden();

                const json = JSON.parse(await initialData.textContent() ?? '{}');
                expect(json.id).toBe(component.id);
                expect(json.blockId).toBe(component.blockId);
                expect(json.elementId).toBe(component.elementId);
                expect(json.filters).toContain('security');

                await expect.poll(() => page.evaluate(() => Alpine.store('LokiComponents').getComponentIds()))
                    .toContain(component.id);

                const registered = await page.evaluate((component) => {
                    const store = Alpine.store('LokiComponents');
                    const byId = store.get(component.id);
                    return {
                        blockId: byId.blockId,
                        elementId: byId.elementId,
                        valid: byId.valid,
                        messages: byId.messages,
                        byElementId: store.getComponentByElementId(component.elementId)?.id,
                        byBlockId: store.getComponentByBlockId(component.blockId)?.id,
                        exists: !!store.exists(byId),
                    };
                }, component);

                expect(registered).toEqual({
                    blockId: component.blockId,
                    elementId: component.elementId,
                    valid: true,
                    messages: [],
                    byElementId: component.id,
                    byBlockId: component.id,
                    exists: true,
                });
            });
        }

        test('throws an error for an unknown component', async ({page}) => {
            const error = await page.evaluate(() => {
                try {
                    Alpine.store('LokiComponents').get('UnknownComponent');
                    return null;
                } catch (e: any) {
                    return e.message;
                }
            });

            expect(error).toBe('No such a component: UnknownComponent');
        });

        test('removes components that are no longer in the DOM', async ({page}) => {
            const ids = await page.evaluate(() => {
                document.getElementById('loki-components-test-dummy2')?.remove();
                const store = Alpine.store('LokiComponents');
                store.removeInvisibleComponents();
                return store.getComponentIds();
            });

            expect(ids).toContain('LokiComponentsTestDummy1');
            expect(ids).not.toContain('LokiComponentsTestDummy2');
        });
    });
}
