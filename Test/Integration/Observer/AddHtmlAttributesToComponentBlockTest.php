<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Observer;

use Dom\HTMLDocument;
use Magento\Framework\App\ObjectManager;
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
