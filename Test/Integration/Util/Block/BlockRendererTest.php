<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Util\Block;

use Loki\Components\Util\Block\BlockRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[AppArea('frontend')]
#[AppIsolation(true)]
class BlockRendererTest extends TestCase
{
    public function testGetReturnsPopulatedLayoutBlock(): void
    {
        $ancestorBlock = $this->createBlock('loki-components.test.ancestor');
        $this->createBlock('loki-components.test.renderer-child', 'Loki_Components::test/dummy.phtml');

        $block = $this->getBlockRenderer()->get($ancestorBlock, 'loki-components.test.renderer-child', ['foo' => 'bar']);

        $this->assertSame('loki-components.test.renderer-child', $block->getNameInLayout());
        $this->assertSame('bar', $block->getData('foo'));
        $this->assertSame($ancestorBlock, $block->getAncestorBlock());
        $this->assertSame('loki-components-test-renderer-child', $block->getUniqId());
    }

    public function testGetThrowsExceptionForUnknownBlock(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No block found with name "loki-components.test.unknown"');

        $this->getBlockRenderer()->get($this->createBlock('loki-components.test.ancestor'), 'loki-components.test.unknown');
    }

    public function testGetThrowsExceptionForTemplateBlockWithoutTemplate(): void
    {
        $this->createBlock('loki-components.test.no-template');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No template found with block "loki-components.test.no-template"');

        $this->getBlockRenderer()->get($this->createBlock('loki-components.test.ancestor'), 'loki-components.test.no-template');
    }

    public function testHtmlRendersBlock(): void
    {
        $this->createBlock('loki-components.test.renderer-child', 'Loki_Components::test/dummy.phtml');

        $html = $this->getBlockRenderer()->html(
            $this->createBlock('loki-components.test.ancestor'),
            'loki-components.test.renderer-child'
        );

        $this->assertStringContainsString('class="loki-components-test-dummy"', $html);
        $this->assertStringContainsString('loki-components.test.renderer-child', $html);
    }

    public function testHtmlOfUnknownBlockOnlyReturnsCommentInDeveloperMode(): void
    {
        $html = $this->getBlockRenderer()->html(
            $this->createBlock('loki-components.test.ancestor'),
            'loki-components.test.unknown'
        );

        $appState = ObjectManager::getInstance()->get(AppState::class);
        if ($appState->getMode() === AppState::MODE_DEVELOPER) {
            $this->assertSame(
                '<!-- WARNING: loki-components.test.unknown: No block found with name "loki-components.test.unknown" -->',
                $html
            );
            return;
        }

        $this->assertSame('', $html);
    }

    private function createBlock(string $name, string $template = ''): Template
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, $name, ['data' => ['template' => $template]]);
        $this->assertInstanceOf(Template::class, $block);

        return $block;
    }

    private function getBlockRenderer(): BlockRenderer
    {
        return ObjectManager::getInstance()->create(BlockRenderer::class);
    }
}
