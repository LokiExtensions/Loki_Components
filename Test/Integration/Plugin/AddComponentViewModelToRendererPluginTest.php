<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Plugin;

use Loki\Base\ViewModel\Block\ChildRenderer as BaseChildRenderer;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Plugin\AddComponentViewModelToRendererPlugin;
use Loki\Components\Util\Block\BlockRenderer;
use Loki\Components\Util\Block\ChildRenderer;
use Loki\Components\Util\Block\TemplateRenderer;
use Magento\Framework\Interception\PluginListInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
#[AppIsolation(true)]
class AddComponentViewModelToRendererPluginTest extends TestCase
{
    #[DataProvider('getRenderers')]
    public function testPluginIsRegistered(string $rendererClass, string $pluginName): void
    {
        $pluginList = Bootstrap::getObjectManager()->get(PluginListInterface::class);
        $pluginInfo = $pluginList->get($rendererClass, []);

        $this->assertArrayHasKey($pluginName, $pluginInfo);
        $this->assertSame(AddComponentViewModelToRendererPlugin::class, $pluginInfo[$pluginName]['instance']);
    }

    public static function getRenderers(): array
    {
        return [
            [BaseChildRenderer::class, 'Loki_Components::addComponentViewModelToBaseChildRenderer'],
            [ChildRenderer::class, 'Loki_Components::addComponentViewModelToChildRenderer'],
            [TemplateRenderer::class, 'Loki_Components::addComponentViewModelToTemplateRenderer'],
            [BlockRenderer::class, 'Loki_Components::addComponentViewModelToBlockRenderer'],
        ];
    }

    #[DataProvider('getRendererCallbacks')]
    public function testComponentViewModelIsPassedToChildBlock(string $rendererName): void
    {
        $viewModel = $this->createMock(ComponentViewModelInterface::class);
        $block = $this->renderWith($rendererName, $viewModel);

        $this->assertSame($viewModel, $block->getViewModel());
    }

    #[DataProvider('getRendererCallbacks')]
    public function testOtherViewModelIsNotPassedToChildBlock(string $rendererName): void
    {
        $viewModel = $this->createMock(ArgumentInterface::class);
        $block = $this->renderWith($rendererName, $viewModel);

        $this->assertNull($block->getViewModel());
        $this->assertSame($viewModel, $block->getAncestorViewModel());
    }

    public static function getRendererCallbacks(): array
    {
        return [
            ['baseChildRenderer'],
            ['childRenderer'],
            ['templateRenderer'],
            ['blockRenderer'],
        ];
    }

    private function renderWith(string $rendererName, ArgumentInterface $viewModel): AbstractBlock
    {
        $objectManager = Bootstrap::getObjectManager();
        $layout = $objectManager->get(LayoutInterface::class);

        $ancestorBlock = $this->createBlock('loki-test.ancestor', ['view_model' => $viewModel]);
        $childBlock = $this->createBlock('loki-test.ancestor.child', ['template' => 'Loki_Components::test/dummy.phtml']);
        $layout->setChild('loki-test.ancestor', 'loki-test.ancestor.child', 'child');

        return match ($rendererName) {
            'baseChildRenderer' => $objectManager->create(BaseChildRenderer::class)->get($ancestorBlock, 'child'),
            'childRenderer' => $objectManager->create(ChildRenderer::class)->get($ancestorBlock, 'child'),
            'templateRenderer' => $objectManager->create(TemplateRenderer::class)->get($ancestorBlock, 'test/dummy'),
            'blockRenderer' => $objectManager->create(BlockRenderer::class)->get($ancestorBlock, $childBlock->getNameInLayout()),
        };
    }

    private function createBlock(string $name, array $data = []): Template
    {
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, $name, ['data' => $data]);
        $this->assertInstanceOf(Template::class, $block);

        return $block;
    }
}
