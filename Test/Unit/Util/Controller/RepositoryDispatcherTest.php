<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Util\Controller;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Filter\Filter;
use Loki\Components\Util\Controller\RepositoryDispatcher;
use Loki\Components\Validator\Validator;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\View\LayoutInterface;
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
