<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Controller;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Filter\Filter;
use Loki\Components\Util\Controller\RepositoryDispatcher;
use Loki\Components\Validator\Validator;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\View\LayoutInterface;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RepositoryDispatcherTest extends TestCase
{
    public function testDispatchesEventEvenWithoutRepository(): void
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getRepository')->willReturn(null);

        $eventManager = $this->createMock(EventManager::class);
        $eventManager->expects($this->once())
            ->method('dispatch')
            ->with('loki_components_repository_dispatch', ['component' => $component]);

        $filter = $this->createMock(Filter::class);
        $filter->expects($this->never())->method('filter');

        $dispatcher = new RepositoryDispatcher($eventManager, $filter, $this->createMock(Validator::class));
        $dispatcher->dispatch($component, 'foo');
    }

    public function testSavesFilteredValueWhenValid(): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->expects($this->once())->method('saveValue')->with('filtered');

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getRepository')->willReturn($repository);

        $filter = $this->createMock(Filter::class);
        $filter->method('filter')->with($component, ' raw ')->willReturn('filtered');

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())->method('validate')->with($component, 'filtered')->willReturn(true);

        $dispatcher = new RepositoryDispatcher($this->createMock(EventManager::class), $filter, $validator);
        $dispatcher->dispatch($component, ' raw ');
    }

    public function testDoesNotSaveInvalidValue(): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->expects($this->never())->method('saveValue');

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getRepository')->willReturn($repository);

        $filter = $this->createMock(Filter::class);
        $filter->method('filter')->willReturnArgument(1);

        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturn(false);

        $dispatcher = new RepositoryDispatcher($this->createMock(EventManager::class), $filter, $validator);
        $dispatcher->dispatch($component, 'invalid');
    }

    #[DataProvider('oversizeValueProvider')]
    public function testRejectsOversizeValueBeforeFiltering(mixed $value): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->expects($this->never())->method('saveValue');

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getRepository')->willReturn($repository);

        $filter = $this->createMock(Filter::class);
        $filter->expects($this->never())->method('filter');

        $dispatcher = new RepositoryDispatcher(
            $this->createMock(EventManager::class),
            $filter,
            $this->createMock(Validator::class),
            100
        );

        $this->expectException(InvalidArgumentException::class);
        $dispatcher->dispatch($component, $value);
    }

    public static function oversizeValueProvider(): array
    {
        return [
            'string' => [str_repeat('a', 101)],
            'nested array' => [['foo' => ['bar' => str_repeat('a', 60), 'baz' => str_repeat('a', 60)]]],
            'many nulls' => [array_fill(0, 60, null)],
            'many integers' => [array_fill(0, 60, 0)],
            'many booleans' => [array_fill(0, 60, true)],
            'many empty arrays' => [array_fill(0, 60, [])],
            'long integers' => [array_fill(0, 10, PHP_INT_MAX)],
        ];
    }

    public function testRejectsValueWithTooManyElements(): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->expects($this->never())->method('saveValue');

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getRepository')->willReturn($repository);

        $dispatcher = new RepositoryDispatcher(
            $this->createMock(EventManager::class),
            $this->createMock(Filter::class),
            $this->createMock(Validator::class),
            65536,
            10
        );

        $this->expectException(InvalidArgumentException::class);
        $dispatcher->dispatch($component, ['a' => [1, 2, 3, 4, 5], 'b' => [1, 2, 3, 4]]);
    }

    public function testAcceptsValueAtMaximumLength(): void
    {
        $value = str_repeat('a', 100);
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->expects($this->once())->method('saveValue')->with($value);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getRepository')->willReturn($repository);

        $filter = $this->createMock(Filter::class);
        $filter->method('filter')->willReturnArgument(1);

        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturn(true);

        $dispatcher = new RepositoryDispatcher($this->createMock(EventManager::class), $filter, $validator, 100);
        $dispatcher->dispatch($component, $value);
    }

    public function testPostDispatchDispatchesEvent(): void
    {
        $layout = $this->createMock(LayoutInterface::class);
        $updates = ['foo'];

        $eventManager = $this->createMock(EventManager::class);
        $eventManager->expects($this->once())
            ->method('dispatch')
            ->with('loki_components_repository_post_dispatch', ['layout' => $layout, 'updates' => $updates]);

        $dispatcher = new RepositoryDispatcher(
            $eventManager,
            $this->createMock(Filter::class),
            $this->createMock(Validator::class)
        );
        $dispatcher->postDispatch($layout, $updates);
    }
}
