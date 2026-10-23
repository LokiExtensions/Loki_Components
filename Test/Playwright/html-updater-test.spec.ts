import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

declare const Alpine: any;
declare const LokiHtmlReplacer: any;

for (const theme of THEMES) {
    test.describe(`Loki Components HTML updater (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('replaces attributes and children of an element with the same tag', async ({page}) => {
            const result = await page.evaluate(() => {
                const root = document.createElement('div');
                root.id = 'loki-components-test-replace';
                root.setAttribute('data-old', 'old');
                root.innerHTML = '<span>Old</span>';
                document.body.appendChild(root);

                const newRoot = document.createElement('div');
                newRoot.id = 'loki-components-test-replace';
                newRoot.setAttribute('data-new', 'new');
                newRoot.innerHTML = '<p>New</p><b>content</b>';

                LokiHtmlReplacer.replace(root, newRoot);

                const current = document.getElementById('loki-components-test-replace');
                return {
                    sameElement: current === root,
                    attributes: root.getAttributeNames().sort(),
                    html: root.innerHTML,
                };
            });

            expect(result.sameElement, 'The original element is kept').toBe(true);
            expect(result.attributes).toEqual(['data-new', 'id']);
            expect(result.html).toBe('<p>New</p><b>content</b>');
        });

        test('keeps x-ignore elements including their state', async ({page}) => {
            const result = await page.evaluate(() => {
                const root = document.createElement('div');
                root.innerHTML = '<p>Old</p><div x-ignore class="ignored"><input value="old"></div>';
                document.body.appendChild(root);

                const oldIgnored = root.querySelector('.ignored') as HTMLElement;
                (oldIgnored.querySelector('input') as HTMLInputElement).value = 'typed by user';

                const newRoot = document.createElement('div');
                newRoot.innerHTML = '<p>New</p><div x-ignore class="ignored"><input value="server"></div>';

                LokiHtmlReplacer.replace(root, newRoot);

                const newIgnored = root.querySelector('.ignored') as HTMLElement;
                return {
                    paragraph: root.querySelector('p')?.textContent,
                    sameIgnoredElement: newIgnored === oldIgnored,
                    inputValue: (newIgnored.querySelector('input') as HTMLInputElement).value,
                };
            });

            expect(result.paragraph).toBe('New');
            expect(result.sameIgnoredElement).toBe(true);
            expect(result.inputValue).toBe('typed by user');
        });

        test('leaves an x-ignore root untouched', async ({page}) => {
            const html = await page.evaluate(() => {
                const root = document.createElement('div');
                root.setAttribute('x-ignore', '');
                root.innerHTML = '<span>Old</span>';
                document.body.appendChild(root);

                const newRoot = document.createElement('div');
                newRoot.innerHTML = '<span>New</span>';

                LokiHtmlReplacer.replace(root, newRoot);
                return root.innerHTML;
            });

            expect(html).toBe('<span>Old</span>');
        });

        test('swaps the element and initializes Alpine.js when the tag changes', async ({page}) => {
            const result = await page.evaluate(async () => {
                const root = document.createElement('div');
                root.id = 'loki-components-test-swap';
                document.body.appendChild(root);

                const newRoot = document.createElement('section');
                newRoot.id = 'loki-components-test-swap';
                newRoot.setAttribute('x-data', 'LokiToggle');
                newRoot.innerHTML = '<span x-show="shown">Shown</span>';

                LokiHtmlReplacer.replace(root, newRoot);
                await new Promise(resolve => requestAnimationFrame(resolve));

                const current = document.getElementById('loki-components-test-swap') as HTMLElement;
                return {
                    tagName: current.tagName,
                    oldElementConnected: root.isConnected,
                    alpineInitialized: !!(current as any)._x_dataStack,
                    spanDisplay: (current.querySelector('span') as HTMLElement).style.display,
                    alpineAvailable: typeof Alpine,
                };
            });

            expect(result.tagName).toBe('SECTION');
            expect(result.oldElementConnected).toBe(false);
            expect(result.alpineInitialized, 'Alpine.js is initialized on the new element').toBe(true);
            expect(result.spanDisplay, 'x-show is applied').toBe('none');
        });
    });
}
