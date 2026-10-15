<?php declare(strict_types=1);

namespace Loki\Components\Test\Integration\Observer;

use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Test\Integration\Dummy\InvisibleViewModelDummy;
use Loki\Components\Test\Integration\Traits\RegisterTestComponents;
use Loki\Components\Util\Block\BlockRenderer;
use Loki\Components\Util\Block\ChildRenderer;
use Loki\Components\Util\Block\ImageRenderer;
use Loki\Components\Util\Block\TemplateRenderer;
use Loki\Components\Util\ComponentUtil;
use Loki\Components\Factory\ViewModelFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class AssignAdditionalBlockVariablesTest extends TestCase
{
    use RegisterTestComponents;

    private const COMPONENT = 'loki-components.test.dummy1';
    private const DUMMY_TEMPLATE = 'Loki_Components::test/dummy.phtml';

    protected function tearDown(): void
    {
        $this->unregisterTestComponents();
    }

    #[AppIsolation(true)]
    public function testAssignsViewModelAndVariablesToComponentBlock(): void
    {
        $block = $this->createBlock(self::COMPONENT);
        $html = $block->toHtml();

        $this->assertStringContainsString('>'.self::COMPONENT.'</div>', $html);
        $this->assertInstanceOf(ComponentViewModelInterface::class, $block->getViewModel());
        $this->assertSame(self::DUMMY_TEMPLATE, $block->getTemplate());

        $variables = $this->getAssignedVariables($block);
        $this->assertInstanceOf(ComponentViewModelInterface::class, $variables['viewModel'] ?? null);
        $this->assertInstanceOf(ComponentUtil::class, $variables['componentUtil'] ?? null);
        $this->assertRendererVariablesAreAssigned($variables);
    }

    #[AppIsolation(true)]
    public function testDisallowedRenderingUsesNotRenderedTemplate(): void
    {
        $block = $this->createBlock(self::COMPONENT, ['allow_rendering' => false]);
        $html = $block->toHtml();

        $this->assertSame('Loki_Components::utils/not-rendered.phtml', $block->getTemplate());
        $this->assertTrue($block->getNotRendered());
        $this->assertStringContainsString('Not rendered: '.self::COMPONENT, $html);
        $this->assertStringNotContainsString('x-data=', $html);
    }

    #[AppIsolation(true)]
    public function testLazyLoadUsesLazyLoadTemplate(): void
    {
        $block = $this->createBlock(self::COMPONENT, ['lazy_load' => true]);
        $block->toHtml();

        $this->assertSame('Loki_Components::utils/lazy-load.phtml', $block->getTemplate());
    }

    #[AppIsolation(true)]
    public function testLazyLoadUsesCustomLazyLoadTemplate(): void
    {
        $block = $this->createBlock(self::COMPONENT, [
            'lazy_load' => true,
            'lazy_load_template' => 'Loki_Components::utils/container.phtml',
        ]);
        $block->toHtml();

        $this->assertSame('Loki_Components::utils/container.phtml', $block->getTemplate());
    }

    #[AppIsolation(true)]
    public function testInvisibleViewModelUsesNotVisibleTemplate(): void
    {
        $this->registerTestComponents([
            $this->createTestComponentDefinition(self::COMPONENT, ['viewModel' => InvisibleViewModelDummy::class]),
        ]);

        $block = $this->createBlock(self::COMPONENT);
        $block->toHtml();

        $this->assertSame('Loki_Components::utils/not-visible.phtml', $block->getTemplate());
    }

    #[AppIsolation(true)]
    public function testAssignsRendererVariablesToBlockWithLokiTemplate(): void
    {
        $block = $this->createBlock('some.regular.block');
        $block->toHtml();

        $variables = $this->getAssignedVariables($block);
        $this->assertArrayNotHasKey('viewModel', $variables);
        $this->assertRendererVariablesAreAssigned($variables);
    }

    #[AppIsolation(true)]
    public function testAssignsRendererVariablesToBlockWithLokiPrefix(): void
    {
        $block = $this->createBlock('loki.regular.block', [], 'Magento_Theme::html/container.phtml');
        $block->toHtml();

        $this->assertRendererVariablesAreAssigned($this->getAssignedVariables($block));
    }

    #[AppIsolation(true)]
    public function testLeavesOtherBlocksAlone(): void
    {
        $block = $this->createBlock('some.regular.block', [], 'Magento_Theme::html/container.phtml');
        $block->toHtml();

        $variables = $this->getAssignedVariables($block);
        $moduleVariables = ['viewModel', 'componentUtil', 'viewModelFactory', 'blockRenderer', 'childRenderer', 'templateRenderer', 'imageRenderer'];
        foreach ($moduleVariables as $moduleVariable) {
            $this->assertArrayNotHasKey($moduleVariable, $variables);
        }

        $this->assertNull($block->getViewModel());
    }

    private function assertRendererVariablesAreAssigned(array $variables): void
    {
        $this->assertInstanceOf(ViewModelFactory::class, $variables['viewModelFactory'] ?? null);
        $this->assertInstanceOf(BlockRenderer::class, $variables['blockRenderer'] ?? null);
        $this->assertInstanceOf(ChildRenderer::class, $variables['childRenderer'] ?? null);
        $this->assertInstanceOf(TemplateRenderer::class, $variables['templateRenderer'] ?? null);
        $this->assertInstanceOf(ImageRenderer::class, $variables['imageRenderer'] ?? null);
    }

    private function getAssignedVariables(Template $block): array
    {
        $property = new \ReflectionProperty(Template::class, '_viewVars');
        return (array)$property->getValue($block);
    }

    private function createBlock(string $name, array $data = [], string $template = self::DUMMY_TEMPLATE): Template
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);

        /** @var Template $block */
        $block = $layout->createBlock(Template::class, $name, ['data' => $data]);
        $block->setTemplate($template); // @phpstan-ignore bitExpertMagento.setTemplateDisallowedForBlock

        return $block;
    }
}
