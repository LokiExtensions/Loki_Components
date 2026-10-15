<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Component;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Component\ComponentViewModel;
use Loki\Components\Filter\Filter;
use Loki\Components\Messages\LocalMessageRegistry;
use Loki\Components\Test\Unit\Mock\BlockMock;
use Loki\Components\Util\JsPropertyResolver;
use Loki\Components\Validator\Validator;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ComponentViewModelTest extends TestCase
{
    use BlockMock;

    private const NAME = 'loki-components.test_example';

    private ?LocalMessageRegistry $localMessageRegistry = null;

    protected function setUp(): void
    {
        $this->localMessageRegistry = new LocalMessageRegistry();
    }

    public function testDefaults(): void
    {
        $viewModel = $this->getViewModel();

        $this->assertFalse($viewModel->isLazyLoad());
        $this->assertFalse($viewModel->isLazyUpdate());
        $this->assertSame(0, $viewModel->getLazyUpdateTimeout());
        $this->assertFalse($viewModel->isSkipQueue());
        $this->assertTrue($viewModel->isAllowRendering());
        $this->assertTrue($viewModel->isVisible());
        $this->assertTrue($viewModel->isValid());
        $this->assertSame('local', $viewModel->getMessageArea());
        $this->assertSame([], $viewModel->getValidators());
        $this->assertSame([], $viewModel->getFilters());
        $this->assertNull($viewModel->getTemplate());
        $this->assertNull($viewModel->getValue());
    }

    public function testBlockDataOverrulesDefaults(): void
    {
        $viewModel = $this->getViewModel([
            'lazy_load' => true,
            'lazy_update' => true,
            'lazy_update_timeout' => '500',
            'skip_queue' => true,
            'allow_rendering' => false,
            'message_area' => 'global',
        ]);

        $this->assertTrue($viewModel->isLazyLoad());
        $this->assertTrue($viewModel->isLazyUpdate());
        $this->assertSame(500, $viewModel->getLazyUpdateTimeout());
        $this->assertTrue($viewModel->isSkipQueue());
        $this->assertFalse($viewModel->isAllowRendering());
        $this->assertSame('global', $viewModel->getMessageArea());
    }

    public function testNonBooleanBlockDataIsIgnored(): void
    {
        $viewModel = $this->getViewModel([
            'lazy_load' => 'yes',
            'lazy_update' => 1,
            'lazy_update_timeout' => 'soon',
            'skip_queue' => 1,
            'allow_rendering' => 0,
        ]);

        $this->assertFalse($viewModel->isLazyLoad());
        $this->assertFalse($viewModel->isLazyUpdate());
        $this->assertSame(0, $viewModel->getLazyUpdateTimeout());
        $this->assertFalse($viewModel->isSkipQueue());
        $this->assertTrue($viewModel->isAllowRendering());
    }

    public function testLazyLoadIsDisabledDuringAjaxRequests(): void
    {
        $viewModel = $this->getViewModel(['lazy_load' => true], true);
        $this->assertFalse($viewModel->isLazyLoad());
    }

    public function testGetBlockFallsBackToLayout(): void
    {
        $block = $this->getBlockMock(self::NAME);

        $layout = $this->createMock(LayoutInterface::class);
        $layout->method('getBlock')->with(self::NAME)->willReturn($block);

        $component = $this->getComponent();
        $component->method('getLayout')->willReturn($layout);

        $viewModel = new ComponentViewModel();
        $viewModel->setComponent($component);

        $this->assertFalse($viewModel->hasBlock());
        $this->assertSame($block, $viewModel->getBlock());
        $this->assertTrue($viewModel->hasBlock());
    }

    public function testGetJsComponentIdAndName(): void
    {
        $viewModel = $this->getViewModel(['js_component_name' => 'LokiFoo']);
        $this->assertSame('LokiComponentsTestExample', $viewModel->getJsComponentId());
        $this->assertSame('LokiFoo', $viewModel->getJsComponentName());
        $this->assertSame(self::NAME, $viewModel->getComponentName());
    }

    public function testIsValidDependsOnErrorMessages(): void
    {
        $viewModel = $this->getViewModel();

        $this->localMessageRegistry->addWarning('Just a warning');
        $this->assertTrue($viewModel->isValid());

        $this->localMessageRegistry->addError('An error');
        $this->assertFalse($viewModel->isValid());
        $this->assertCount(2, $viewModel->getMessages());
    }

    public function testGetValueFiltersAndValidatesRepositoryValue(): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn(' raw ');

        $component = $this->getComponent();
        $component->method('hasRepository')->willReturn(true);
        $component->method('getRepository')->willReturn($repository);
        $component->method('isValidated')->willReturn(false);

        $filter = $this->createMock(Filter::class);
        $filter->method('filter')->with($component, ' raw ')->willReturn('raw');

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())->method('validate')->with($component, 'raw');

        $viewModel = $this->getViewModel([], false, $component);
        $viewModel->setFilter($filter);
        $viewModel->setValidator($validator);

        $this->assertSame('raw', $viewModel->getValue());
    }

    public function testGetValueSkipsValidationWhenAlreadyValidated(): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn('raw');

        $component = $this->getComponent();
        $component->method('hasRepository')->willReturn(true);
        $component->method('getRepository')->willReturn($repository);
        $component->method('isValidated')->willReturn(true);

        $filter = $this->createMock(Filter::class);
        $filter->method('filter')->willReturnArgument(1);

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->never())->method('validate');

        $viewModel = $this->getViewModel([], false, $component);
        $viewModel->setFilter($filter);
        $viewModel->setValidator($validator);

        $this->assertSame('raw', $viewModel->getValue());
    }

    public function testGetJsData(): void
    {
        $viewModel = $this->getViewModel([
            'js_data' => ['custom' => 'foobar', 'lazyLoad' => 'overruled'],
            'lazy_update' => true,
        ]);

        $jsData = $viewModel->getJsData();

        $this->assertSame('foobar', $jsData['custom']);
        $this->assertFalse($jsData['lazyLoad'], 'Internal properties overrule block JS data');
        $this->assertTrue($jsData['lazyUpdate']);
        $this->assertSame(0, $jsData['lazyUpdateTimeout']);
        $this->assertFalse($jsData['skipQueue']);
        $this->assertNull($jsData['value']);
        $this->assertSame([], $jsData['messages']);
        $this->assertSame('local', $jsData['messageArea']);
        $this->assertTrue($jsData['valid']);
        $this->assertFalse($jsData['skipValidation']);
    }

    public function testGetTargetsAreTakenFromComponent(): void
    {
        $component = $this->getComponent();
        $component->method('getTargets')->willReturn(['foo']);
        $component->method('getTargetString')->willReturn('foo');

        $viewModel = $this->getViewModel([], false, $component);
        $this->assertSame(['foo'], $viewModel->getTargets());
        $this->assertSame('foo', $viewModel->getTargetString());
    }

    private function getViewModel(
        array $blockData = [],
        bool $isAjax = false,
        ?ComponentInterface $component = null
    ): ComponentViewModel {
        if (null === $component) {
            $component = $this->getComponent($isAjax);
        }

        $viewModel = new ComponentViewModel();
        $viewModel->setComponent($component);
        $viewModel->setBlock($this->getBlockMock(self::NAME, $blockData));

        return $viewModel;
    }

    private function getComponent(bool $isAjax = false): ComponentInterface&MockObject
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getName')->willReturn(self::NAME);
        $component->method('isAjax')->willReturn($isAjax);
        $component->method('getLocalMessageRegistry')->willReturn($this->localMessageRegistry);
        $component->method('getJsPropertyResolver')->willReturn(new JsPropertyResolver());

        return $component;
    }
}
