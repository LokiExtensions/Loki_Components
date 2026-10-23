import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, TEST_PAGE_HANDLES, THEMES, waitForLokiComponents} from './helpers/configure';

const MODAL_ID = 'loki-components-test-modal';

for (const theme of THEMES) {
    test.describe(`Loki Components modal (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page, TEST_PAGE_HANDLES);
            await waitForLokiComponents(page);
            await page.evaluate(() => localStorage.removeItem('loki-modals'));
        });

        test('opens and closes the modal', async ({page}) => {
            const dialog = page.locator('#' + MODAL_ID + ' dialog');
            await expect(dialog).toBeHidden();

            await page.locator('#' + MODAL_ID + ' .loki-components-test-modal-open').click();
            await expect(dialog).toBeVisible();
            await expect(dialog).toHaveAttribute('open');

            await dialog.locator('.loki-components-test-modal-close').click();
            await expect(dialog).toBeHidden();
        });

        test('closes the modal with the Escape key', async ({page}) => {
            const dialog = page.locator('#' + MODAL_ID + ' dialog');
            await page.locator('#' + MODAL_ID + ' .loki-components-test-modal-open').click();
            await expect(dialog).toBeVisible();

            await page.keyboard.press('Escape');
            await expect(dialog).toBeHidden();
        });

        test('closes the modal when clicking the backdrop', async ({page}) => {
            const dialog = page.locator('#' + MODAL_ID + ' dialog');
            await page.locator('#' + MODAL_ID + ' .loki-components-test-modal-open').click();
            await expect(dialog).toBeVisible();

            await dialog.dispatchEvent('click');
            await expect(dialog).toBeHidden();
        });

        test('saves the modal state to local storage', async ({page}) => {
            const getModalState = () => page.evaluate(() => JSON.parse(localStorage.getItem('loki-modals') ?? '{}'));

            await page.locator('#' + MODAL_ID + ' .loki-components-test-modal-open').click();
            await expect.poll(getModalState).toEqual({[MODAL_ID]: true});

            await page.locator('#' + MODAL_ID + ' .loki-components-test-modal-close').click();
            await expect.poll(getModalState).toEqual({});
        });

        test('restores an open modal after a reload', async ({page}) => {
            await page.locator('#' + MODAL_ID + ' .loki-components-test-modal-open').click();
            await expect(page.locator('#' + MODAL_ID + ' dialog')).toBeVisible();

            await page.reload();
            await waitForLokiComponents(page);

            await expect(page.locator('#' + MODAL_ID + ' dialog')).toBeVisible();
        });
    });
}
