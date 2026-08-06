import {setupCheckout} from '@loki/setup-checkout';
import {test, expect} from '@loki/test';
import coreConfig from '@loki/config';

declare const Alpine: any;
declare const LokiComponentExtender: any;

const APPENDED_VALUE = 'appended-by-playwright';

test.describe('Loki Components extender appends data', function () {
    test('appends data after the original component data is generated', async function ({page, context}) {
        await setupCheckout(page, context, coreConfig);

        const componentData = await page.evaluate(async (appendedValue) => {
            LokiComponentExtender.addMixin('PlaywrightMixin', 'PlaywrightExtenderTest', {
                mixinValue: 'from-mixin',
                sharedValue: 'from-mixin'
            });

            LokiComponentExtender.addMixin(
                'PlaywrightAppendMixin',
                'PlaywrightExtenderTest',
                (componentData) => ({
                    appendedValue: appendedValue,
                    sharedValue: 'from-append',
                    seenBaseValue: componentData.baseValue,
                    seenMixinValue: componentData.mixinValue,
                    greet() {
                        return componentData.greet.call(this) + ' + append';
                    }
                }),
                true
            );

            Alpine.data('PlaywrightExtenderTest', () => ({
                baseValue: 'from-base',
                sharedValue: 'from-base',
                greet() {
                    return 'base(' + this.baseValue + ')';
                }
            }));

            const element = document.createElement('div');
            element.id = 'playwright-extender-test';
            element.setAttribute('x-data', 'PlaywrightExtenderTest');
            document.body.appendChild(element);

            await new Promise((resolve) => setTimeout(resolve, 250));

            const data = Alpine.$data(element);

            return {
                baseValue: data.baseValue,
                mixinValue: data.mixinValue,
                appendedValue: data.appendedValue,
                sharedValue: data.sharedValue,
                seenBaseValue: data.seenBaseValue,
                seenMixinValue: data.seenMixinValue,
                greeting: data.greet()
            };
        }, APPENDED_VALUE);

        expect(componentData.baseValue, 'Original component data').toBe('from-base');
        expect(componentData.mixinValue, 'Data of a regular mixin').toBe('from-mixin');
        expect(componentData.appendedValue, 'Data of an appending mixin').toBe(APPENDED_VALUE);
        expect(componentData.sharedValue, 'An appending mixin overrules a regular mixin').toBe('from-append');
        expect(componentData.seenBaseValue, 'An appending mixin sees the original component data').toBe('from-base');
        expect(componentData.seenMixinValue, 'An appending mixin sees the data of a regular mixin').toBe('from-mixin');
        expect(componentData.greeting, 'An appending mixin calls the original method').toBe('base(from-base) + append');
    });

    test('appends data to the components rendered in the checkout', async function ({page, context}) {
        await page.addInitScript((appendedValue) => {
            document.addEventListener('alpine:init', () => {
                LokiComponentExtender.add(
                    'PlaywrightAppendExtension',
                    (componentName, componentData) => ({
                        playwrightAppendedValue: appendedValue,
                        playwrightAppendedFor: componentName,
                        playwrightSawComponentData: typeof componentData.init === 'function'
                    }),
                    true
                );
            });
        }, APPENDED_VALUE);

        await setupCheckout(page, context, coreConfig);

        const components = await page.evaluate(() => {
            return Alpine.store('LokiComponents').getComponentArray().map((component) => ({
                id: component.id,
                appendedValue: component.playwrightAppendedValue,
                appendedFor: component.playwrightAppendedFor,
                sawComponentData: component.playwrightSawComponentData
            }));
        });

        expect(components.length, 'Registered components in the checkout').toBeGreaterThan(0);

        components.forEach((component) => {
            expect(component.appendedValue, 'Appended value of component ' + component.id).toBe(APPENDED_VALUE);
            expect(component.appendedFor, 'Component name of component ' + component.id).toBeTruthy();
            expect(component.sawComponentData, 'Component data seen by component ' + component.id).toBe(true);
        });
    });
});
