import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

const TABS_ID = 'loki-components-test-tabs';

for (const theme of THEMES) {
    test.describe(`Loki Components tabs (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
        });

        test('shows the initially active tab', async ({page}) => {
            const tabs = page.locator('#' + TABS_ID);

            await expect(tabs.locator('[data-tab-id="one"]')).toHaveClass(/tab-active/);
            await expect(tabs.locator('[data-tab-id="one"]')).toHaveAttribute('aria-selected', 'true');
            await expect(tabs.locator('[data-tab-id="two"]')).toHaveClass(/tab-inactive/);
            await expect(tabs.locator('[data-tab-content="one"]')).toHaveClass(/\bblock\b/);
            await expect(tabs.locator('[data-tab-content="two"]')).toHaveClass(/\bhidden\b/);
        });

        test('switches to another tab when clicked', async ({page}) => {
            const tabs = page.locator('#' + TABS_ID);
            await tabs.locator('[data-tab-id="two"]').click();

            await expect(tabs.locator('[data-tab-id="two"]')).toHaveClass(/tab-active/);
            await expect(tabs.locator('[data-tab-id="two"] input[type="radio"]')).toBeChecked();
            await expect(tabs.locator('[data-tab-id="one"]')).toHaveClass(/tab-inactive/);
            await expect(tabs.locator('[data-tab-id="one"]')).toHaveAttribute('aria-selected', 'false');
            await expect(tabs.locator('[data-tab-content="two"]')).toHaveClass(/\bblock\b/);
            await expect(tabs.locator('[data-tab-content="one"]')).toHaveClass(/\bhidden\b/);

            await tabs.locator('[data-tab-id="one"]').click();
            await expect(tabs.locator('[data-tab-id="one"]')).toHaveClass(/tab-active/);
            await expect(tabs.locator('[data-tab-content="one"]')).toHaveClass(/\bblock\b/);
        });
    });
}
