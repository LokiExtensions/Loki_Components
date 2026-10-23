<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Block;

use Loki\Components\Util\Block\TemplateRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
#[AppIsolation(true)]
class TemplateRendererTest extends TestCase
{
    #[DataProvider('getTemplateNames')]
    public function testTemplateNameIsNormalized(string $templateName, string $alias): void
    {
        $block = $this->getTemplateRenderer()->get($this->createAncestorBlock(), $templateName, ['alias' => $alias]);

        $this->assertSame('Loki_Components::test/dummy.phtml', $block->getTemplate());
        $this->assertSame('loki-components.test.ancestor.' . $alias, $block->getNameInLayout());
    }

    public static function getTemplateNames(): array
    {
        return [
            ['test/dummy', 'short'],
            ['test/dummy.phtml', 'with-suffix'],
            ['Loki_Components::test/dummy', 'without-suffix'],
            ['Loki_Components::test/dummy.phtml', 'full'],
        ];
    }

    public function testDataIsPopulated(): void
    {
        $ancestorBlock = $this->createAncestorBlock();
        $block = $this->getTemplateRenderer()->get($ancestorBlock, 'test/dummy', ['alias' => 'foo', 'bar' => 'baz']);

        $this->assertSame('baz', $block->getData('bar'));
        $this->assertSame($ancestorBlock, $block->getAncestorBlock());
        $this->assertSame('loki-components-test-ancestor-foo', $block->getUniqId());
    }

    public function testExistingBlockIsReused(): void
    {
        $ancestorBlock = $this->createAncestorBlock();
        $templateRenderer = $this->getTemplateRenderer();

        $block1 = $templateRenderer->get($ancestorBlock, 'test/dummy', ['alias' => 'foo']);
        $block2 = $templateRenderer->get($ancestorBlock, 'test/dummy', ['alias' => 'foo']);

        $this->assertSame($block1, $block2);
    }

    public function testHtml(): void
    {
        $html = $this->getTemplateRenderer()->html($this->createAncestorBlock(), 'test/dummy', ['alias' => 'foo']);

        $this->assertStringContainsString('class="loki-components-test-dummy"', $html);
        $this->assertStringContainsString('loki-components.test.ancestor.foo', $html);
    }

    private function createAncestorBlock(): Template
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, 'loki-components.test.ancestor');
        $this->assertInstanceOf(Template::class, $block);

        return $block;
    }

    private function getTemplateRenderer(): TemplateRenderer
    {
        return ObjectManager::getInstance()->create(TemplateRenderer::class);
    }
}
