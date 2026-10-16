<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Observer;

use Dom\HTMLDocument;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Loki\Components\Observer\AddHtmlAttributesToComponentBlock;
use Loki\Components\Util\AppMode;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class AddHtmlAttributesToComponentBlockTest extends TestCase
{
    private const BLOCK_NAME = 'loki-components.test.dummy1';

    #[DataProvider('getHtmlPayloads')]
    #[AppIsolation(true)]
    public function testHtmlInJsDataIsNotRenderedAsMarkup(string $payload): void
    {
        $html = $this->renderComponentWithJsData(['html' => $payload]);

        $this->assertStringContainsString('x-ref="initialData"', $html);
        $rawJsonValue = substr((string)json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1);
        $this->assertStringNotContainsString($rawJsonValue, $html, 'Raw HTML leaked into the initial data element');
    }

    #[DataProvider('getHtmlPayloads')]
    #[AppIsolation(true)]
    public function testInitialDataSurvivesHtmlParsing(string $payload): void
    {
        if (false === class_exists(HTMLDocument::class)) {
            $this->markTestSkipped('Requires the HTML5 parser of PHP 8.4+');
        }

        $html = $this->renderComponentWithJsData(['html' => $payload]);

        $document = HTMLDocument::createFromString('<!DOCTYPE html><body>' . $html . '</body>', LIBXML_NOERROR);
        $dataElement = $document->querySelector('data[x-ref="initialData"]');
        $this->assertNotNull($dataElement, 'No initial data element found');

        $jsonData = json_decode((string)$dataElement->textContent, true);
        $this->assertIsArray($jsonData, 'Initial data is no longer valid JSON once parsed as HTML: ' . $dataElement->textContent);
        $this->assertSame(self::BLOCK_NAME, $jsonData['blockId'] ?? null);
        $this->assertSame($payload, $jsonData['html'] ?? null);
    }

    #[AppIsolation(true)]
    public function testAddsComponentAttributesToFirstElement(): void
    {
        $html = $this->renderComponent(self::BLOCK_NAME, [
            'html_attributes' => [
                'data-foo' => 'bar',
                '@click' => 'toggle()',
                'class' => 'overruled',
            ],
        ]);

        $firstTag = $this->getFirstTag($html);
        $this->assertStringContainsString(' id="loki-components-test-dummy1"', $firstTag);
        $this->assertStringContainsString(' x-data="LokiComponent"', $firstTag);
        $this->assertStringContainsString(' data-foo="bar"', $firstTag);
        $this->assertStringContainsString(' x-on:click="toggle()"', $firstTag, 'Alpine shorthands are converted');
        $this->assertStringNotContainsString('@click', $firstTag);
        $this->assertSame(1, substr_count($firstTag, 'class="'), 'Existing attributes are not duplicated');
        $this->assertStringContainsString('class="loki-components-test-dummy"', $firstTag);

        $appMode = ObjectManager::getInstance()->get(AppMode::class);
        if ($appMode->isDeveloperMode()) {
            $this->assertStringContainsString(' x-title="LokiComponentsTestDummy1"', $firstTag);
        } else {
            $this->assertStringNotContainsString('x-title', $firstTag);
        }
    }

    #[AppIsolation(true)]
    public function testUsesCustomJsComponentName(): void
    {
        $html = $this->renderComponent(self::BLOCK_NAME, ['js_component_name' => 'LokiCustomComponent']);
        $this->assertStringContainsString(' x-data="LokiCustomComponent"', $this->getFirstTag($html));
    }

    #[AppIsolation(true)]
    public function testInitialDataContainsComponentData(): void
    {
        $data = $this->getInitialData($this->renderComponent(self::BLOCK_NAME, ['js_data' => ['foo' => 'bar']]));

        $this->assertSame('LokiComponentsTestDummy1', $data['id']);
        $this->assertSame('LokiComponent', $data['name']);
        $this->assertSame(self::BLOCK_NAME, $data['blockId']);
        $this->assertSame('loki-components-test-dummy1', $data['elementId']);
        $this->assertContains('security', $data['filters']);
        $this->assertSame('bar', $data['foo']);
        $this->assertArrayNotHasKey('ajax', $data);
    }

    #[AppIsolation(true)]
    public function testInitialDataIsFlaggedDuringAjaxRequests(): void
    {
        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));
        ObjectManager::getInstance()->get(HttpRequest::class)->setHeaders($headers);

        $data = $this->getInitialData($this->renderComponent(self::BLOCK_NAME));
        $this->assertTrue($data['ajax']);
    }

    #[AppIsolation(true)]
    public function testSkipsBlocksThatAreNoComponent(): void
    {
        $html = $this->renderComponent('loki-components.test.no-component');

        $this->assertStringNotContainsString('x-data=', $html);
        $this->assertStringNotContainsString('x-ref="initialData"', $html);
    }

    #[AppIsolation(true)]
    public function testSkipsBlocksThatAreNotRendered(): void
    {
        $html = $this->renderComponent(self::BLOCK_NAME, ['allow_rendering' => false]);

        $this->assertStringContainsString('Not rendered: '.self::BLOCK_NAME, $html);
        $this->assertStringNotContainsString('x-data=', $html);
    }

    #[DataProvider('getRootElements')]
    #[AppIsolation(true)]
    public function testAddsComponentAttributesToAnyRootElement(string $html, string $expectedTagStart): void
    {
        $result = $this->applyObserver($html);

        $this->assertStringStartsWith($expectedTagStart, $result);
        $this->assertStringContainsString(' id="loki-components-test-dummy1"', $result);
        $this->assertStringContainsString(' x-data="LokiComponent"', $result);
        $this->assertStringContainsString('x-ref="initialData"', $result);
    }

    public static function getRootElements(): array
    {
        return [
            'paragraph' => ['<p class="foo">bar</p>', '<p class="foo"'],
            'anchor' => ['<a href="#">bar</a>', '<a href="#"'],
            'heading' => ['<h2>bar</h2>', '<h2 '],
            'uppercase' => ['<DIV class="foo">bar</DIV>', '<DIV class="foo"'],
        ];
    }

    #[DataProvider('getRootElementsWithSpecialAttributes')]
    #[AppIsolation(true)]
    public function testKeepsExistingAttributesIntact(string $html, string $expectedAttribute, string $expectedContent): void
    {
        $result = $this->applyObserver($html);
        $firstTag = $this->getFirstTagRespectingQuotes($result);

        $this->assertStringContainsString($expectedAttribute, $firstTag);
        $this->assertStringContainsString(' x-data="LokiComponent"', $firstTag);
        $this->assertStringContainsString(' id="loki-components-test-dummy1"', $firstTag);
        $this->assertStringContainsString($expectedContent, $result);
    }

    public static function getRootElementsWithSpecialAttributes(): array
    {
        return [
            'arrow function' => [
                '<div x-on:click="() => open = !open">content</div>',
                'x-on:click="() => open = !open"',
                '</data>content</div>',
            ],
            'comparison' => [
                '<div x-show="count > 0">content</div>',
                'x-show="count > 0"',
                '</data>content</div>',
            ],
            'dollar sign followed by digits' => [
                '<div data-price="$10">content</div>',
                'data-price="$10"',
                '</data>content</div>',
            ],
            'backslash followed by digit' => [
                '<div data-pattern="\1">content</div>',
                'data-pattern="\1"',
                '</data>content</div>',
            ],
        ];
    }

    #[AppIsolation(true)]
    public function testEscapesHtmlAttributeValues(): void
    {
        $html = $this->renderComponent(self::BLOCK_NAME, [
            'html_attributes' => ['data-foo' => 'a"b'],
        ]);

        $this->assertStringContainsString(' data-foo="a&quot;b"', $this->getFirstTag($html));
    }

    private function applyObserver(string $html): string
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);

        /** @var Template $block */
        $block = $layout->createBlock(Template::class, self::BLOCK_NAME);
        $block->setTemplate('Loki_Components::test/dummy.phtml'); // @phpstan-ignore bitExpertMagento.setTemplateDisallowedForBlock

        $transport = new DataObject(['html' => $html]);
        $event = new Event(['block' => $block, 'transport' => $transport]);
        $observer = new Observer(['event' => $event]);

        ObjectManager::getInstance()->get(AddHtmlAttributesToComponentBlock::class)->execute($observer);

        return (string)$transport->getHtml();
    }

    private function getFirstTagRespectingQuotes(string $html): string
    {
        $this->assertMatchesRegularExpression('/^<[a-zA-Z][^\s>]*(?:"[^"]*"|\'[^\']*\'|[^\'">])*>/', $html);
        preg_match('/^<[a-zA-Z][^\s>]*(?:"[^"]*"|\'[^\']*\'|[^\'">])*>/', $html, $match);

        return $match[0];
    }

    private function renderComponent(string $blockName, array $data = []): string
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);

        /** @var Template $block */
        $block = $layout->createBlock(Template::class, $blockName, ['data' => $data]);
        $block->setTemplate('Loki_Components::test/dummy.phtml'); // @phpstan-ignore bitExpertMagento.setTemplateDisallowedForBlock

        return $block->toHtml();
    }

    private function getFirstTag(string $html): string
    {
        $this->assertMatchesRegularExpression('/^<[^>]+>/', $html);
        preg_match('/^<[^>]+>/', $html, $match);

        return $match[0];
    }

    private function getInitialData(string $html): array
    {
        $this->assertMatchesRegularExpression('#<data hidden x-ref="initialData">(.*?)</data>#s', $html);
        preg_match('#<data hidden x-ref="initialData">(.*?)</data>#s', $html, $match);

        $data = json_decode($match[1], true);
        $this->assertIsArray($data, 'Initial data is not valid JSON: '.$match[1]);

        return $data;
    }

    public static function getHtmlPayloads(): array
    {
        return [
            'closing data tag' => ['Store pickup</data><b>Amsterdam</b>'],
            'textarea' => ['<textarea>Opening hours'],
            'bold' => ['<b>Store</b> pickup'],
            'table' => ['<table><tr><td>Store</td></tr></table>'],
            'competing x-ref' => ['<div x-ref="initialData">{}</div>'],
        ];
    }

    private function renderComponentWithJsData(array $jsData): string
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);

        /** @var Template $block */
        $block = $layout->createBlock(Template::class, self::BLOCK_NAME);
        $block->setTemplate('Loki_Components::test/dummy.phtml'); // @phpstan-ignore bitExpertMagento.setTemplateDisallowedForBlock
        $block->setJsData($jsData);

        return $block->toHtml();
    }
}
