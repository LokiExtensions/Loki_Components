import {expect, Page} from '@playwright/test';

export const THEMES = ['Magento/luma', 'Hyva/default-csp'];

export const TEST_PAGE_HANDLES = ['loki_components_index_test'];

export const isHyva = (theme: string): boolean => theme.startsWith('Hyva/');

export async function configure(page: Page, config: object) {
    const response = await page.request.post(
        '/loki_functional_tests/index/configure?token=' + encodeURIComponent(getToken()),
        {form: {config: JSON.stringify(config)}}
    );

    const body = await response.text();
    expect(response.status(), 'Configure response: ' + body).toBe(200);

    const data = JSON.parse(body);
    expect(data.error, 'Configure error').toBeUndefined();
    expect(data.result?.theme?.errors, 'Theme errors').toBeUndefined();
}

/**
 * URL of the Loki_FunctionalTests page with the test components of this module, usable with page.goto() options
 */
export function getTestPageUrl(): string {
    return '/loki_functional_tests/index/page?token=' + encodeURIComponent(getToken())
        + '&handles=' + encodeURIComponent(TEST_PAGE_HANDLES.join(','));
}

export async function waitForLokiComponents(page: Page) {
    await page.waitForFunction(() => {
        const Alpine = (window as any).Alpine;
        return Alpine
            && Alpine.store('LokiComponents')
            && Alpine.store('Message')
            && Alpine.store('LokiComponents').getComponentIds().length > 0;
    });
}

function getToken(): string {
    const token = process.env.TEST_TOKEN;
    if (!token) {
        throw new Error('TEST_TOKEN is not set');
    }

    return token;
}
