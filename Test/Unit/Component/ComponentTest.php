<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Component;

use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentContextInterface;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Component\ComponentViewModel;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Filter\Filter;
use Loki\Components\Messages\GlobalMessageRegistry;
use Loki\Components\Messages\LocalMessageRegistry;
use Loki\Components\Messages\LocalMessageRegistryFactory;
use Loki\Components\Test\Unit\Mock\BlockMock;
use Loki\Components\Util\Ajax;
use Loki\Components\Util\JsPropertyResolver;
use Loki\Components\Validator\Validator;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
    use BlockMock;

    private const NAME = 'loki-components.test.example';

    private ?ObjectManagerInterface $objectManager = null;
    private ?LayoutInterface $layout = null;
    private ?Validator $validator = null;
    private ?Filter $filter = null;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManagerInterface::class);
        $this->layout = $this->createMock(LayoutInterface::class);
        $this->validator = $this->createMock(Validator::class);
        $this->filter = $this->createMock(Filter::class);
    }

    public function testGetters(): void
    {
        $context = $this->createMock(ComponentContextInterface::class);
        $component = $this->getComponent(['context' => $context, 'targets' => ['foo']]);

        $this->assertSame(self::NAME, $component->getName());
        $this->assertSame(['foo'], $component->getTargets());
        $this->assertSame($context, $component->getContext());
        $this->assertSame($this->layout, $component->getLayout());
        $this->assertFalse($component->isValidated());

        $component->setIsValidated(true);
        $this->assertTrue($component->isValidated());
    }

    public function testGetTargetStringSanitizesTargets(): void
    {
        $component = $this->getComponent(['targets' => ['foo.bar', 'baz_qux', 'checkout-step']]);
        $this->assertSame('foo-bar baz-qux checkout-step', $component->getTargetString());
    }

    public function testIsAjax(): void
    {
        $ajax = $this->createMock(Ajax::class);
        $ajax->method('isAjax')->willReturn(true);

        $this->assertTrue($this->getComponent(['ajax' => $ajax])->isAjax());
    }

    public function testGetFiltersAlwaysContainsSecurityFilter(): void
    {
        $this->assertSame(['security'], $this->getComponent()->getFilters());
    }

    public function testGetFiltersMergesAllSources(): void
    {
        $component = $this->getComponent(['filters' => ['trim']]);
        $component->setBlock($this->getBlockMock(self::NAME, ['filters' => ['lowercase', 'trim']]));

        $this->assertSame(['trim', 'uppercase', 'security', 'lowercase'], $component->getFilters(['uppercase']));
    }

    public function testGetValidatorsMergesAllSources(): void
    {
        $viewModel = $this->getViewModelMock();
        $viewModel->method('getValidators')->willReturn(['email', 'required']);
        $this->expectViewModelCreation($viewModel);

        $component = $this->getComponent(['validators' => ['required']]);
        $component->setBlock($this->getBlockMock(self::NAME, ['validators' => ['length']]));

        $this->assertSame(['required', 'number', 'email', 'length'], $component->getValidators(['number']));
    }

    public function testGetViewModelIsCreatedOnceAndPrepared(): void
    {
        $block = $this->getBlockMock(self::NAME);

        $viewModel = $this->getViewModelMock();
        $viewModel->expects($this->once())->method('setBlock')->with($block);
        $viewModel->expects($this->once())->method('setValidator')->with($this->validator);
        $viewModel->expects($this->once())->method('setFilter')->with($this->filter);
        $this->expectViewModelCreation($viewModel);

        $component = $this->getComponent();
        $component->setBlock($block);
        $viewModel->expects($this->once())->method('setComponent')->with($component);

        $this->assertSame($viewModel, $component->getViewModel());
        $this->assertSame($viewModel, $component->getViewModel());
        $this->assertTrue($component->hasViewModel());
    }

    public function testGetViewModelWithoutBlockDoesNotSetBlock(): void
    {
        $viewModel = $this->getViewModelMock();
        $viewModel->expects($this->never())->method('setBlock');
        $this->expectViewModelCreation($viewModel);

        $this->layout->method('getBlock')->willReturn(false);
        $this->getComponent()->getViewModel();
    }

    public function testGetRepositoryWithoutRepositoryClass(): void
    {
        $this->objectManager->expects($this->never())->method('create');

        $this->assertNull($this->getComponent()->getRepository());
        $this->assertFalse($this->getComponent()->hasRepository());
        $this->assertNull($this->getComponent(['repositoryClass' => 'Foo'])->getRepository());
    }

    public function testGetRepositoryIsCreatedOnce(): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $this->objectManager->expects($this->once())
            ->method('create')
            ->with('Loki\\Components\\Test\\DummyRepository')
            ->willReturn($repository);

        $component = $this->getComponent(['repositoryClass' => 'Loki\\Components\\Test\\DummyRepository']);
        $repository->expects($this->once())->method('setComponent')->with($component);

        $this->assertSame($repository, $component->getRepository());
        $this->assertTrue($component->hasRepository());
    }

    public function testGetBlockFromLayout(): void
    {
        $block = $this->getBlockMock(self::NAME);
        $this->layout->expects($this->once())->method('getBlock')->with(self::NAME)->willReturn($block);

        $component = $this->getComponent();
        $this->assertSame($block, $component->getBlock());
        $this->assertSame($block, $component->getBlock());
    }

    public function testGetBlockReturnsNullWhenNotInLayout(): void
    {
        $this->layout->method('getBlock')->willReturn(false);
        $this->assertNull($this->getComponent()->getBlock());
    }

    public function testDispatchLocalMessages(): void
    {
        $this->layout->method('getBlock')->willReturn(false);
        $this->assertTrue($this->getComponent()->dispatchLocalMessages());

        $component = $this->getComponent();
        $component->setBlock($this->getBlockMock(self::NAME, ['dispatch_local_messages' => false]));
        $this->assertFalse($component->dispatchLocalMessages());

        $component = $this->getComponent();
        $component->setBlock($this->getBlockMock(self::NAME, ['dispatch_local_messages' => 'invalid']));
        $this->assertTrue($component->dispatchLocalMessages());
    }

    public function testGetLocalMessageRegistryIsCreatedOnce(): void
    {
        $localMessageRegistry = new LocalMessageRegistry();
        $localMessageRegistryFactory = $this->createMock(LocalMessageRegistryFactory::class);
        $localMessageRegistryFactory->expects($this->once())->method('create')->willReturn($localMessageRegistry);

        $component = $this->getComponent(['localMessageRegistryFactory' => $localMessageRegistryFactory]);
        $this->assertSame($localMessageRegistry, $component->getLocalMessageRegistry());
        $this->assertSame($localMessageRegistry, $component->getLocalMessageRegistry());
    }

    private function expectViewModelCreation(ComponentViewModelInterface $viewModel): void
    {
        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(ComponentViewModel::class)
            ->willReturn($viewModel);
    }

    private function getViewModelMock(): ComponentViewModelInterface&MockObject
    {
        return $this->createMock(ComponentViewModelInterface::class);
    }

    private function getComponent(array $arguments = []): Component
    {
        return new Component(
            $this->objectManager,
            $this->layout,
            $arguments['context'] ?? $this->createMock(ComponentContextInterface::class),
            $this->createMock(GlobalMessageRegistry::class),
            $arguments['localMessageRegistryFactory'] ?? $this->createMock(LocalMessageRegistryFactory::class),
            new JsPropertyResolver(),
            $this->validator,
            $this->filter,
            $arguments['ajax'] ?? $this->createMock(Ajax::class),
            self::NAME,
            $arguments['targets'] ?? [],
            $arguments['validators'] ?? [],
            $arguments['filters'] ?? [],
            $arguments['repositoryClass'] ?? null,
            ComponentViewModel::class,
        );
    }
}
