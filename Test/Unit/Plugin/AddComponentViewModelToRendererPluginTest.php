<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Plugin;

use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Plugin\AddComponentViewModelToRendererPlugin;
use Loki\Components\Test\Unit\Mock\BlockMock;
use Loki\Components\Util\Block\AbstractRenderer;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use PHPUnit\Framework\TestCase;

class AddComponentViewModelToRendererPluginTest extends TestCase
{
    use BlockMock;

    public function testPassesComponentViewModelToChildBlock(): void
    {
        $viewModel = $this->createMock(ComponentViewModelInterface::class);
        $childBlock = $this->getBlockMock('child');

        $result = (new AddComponentViewModelToRendererPlugin())->afterPopulateBlock(
            $this->getRenderer(['view_model' => $viewModel]),
            'return-value',
            $childBlock
        );

        $this->assertSame('return-value', $result);
        $this->assertSame($viewModel, $childBlock->getViewModel());
    }

    public function testIgnoresRegularViewModel(): void
    {
        $childBlock = $this->getBlockMock('child');

        (new AddComponentViewModelToRendererPlugin())->afterPopulateBlock(
            $this->getRenderer(['view_model' => $this->createMock(ArgumentInterface::class)]),
            null,
            $childBlock
        );

        $this->assertNull($childBlock->getViewModel());
    }

    private function getRenderer(array $ancestorBlockData): AbstractRenderer
    {
        $renderer = $this->createMock(AbstractRenderer::class);
        $renderer->method('getAncestorBlock')->willReturn($this->getBlockMock('ancestor', $ancestorBlockData));

        return $renderer;
    }
}
