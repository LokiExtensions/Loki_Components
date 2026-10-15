<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Layout;

use Loki\Components\Layout\LayoutHandlerComposite;
use Loki\Components\Layout\LayoutHandlerInterface;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

class LayoutHandlerCompositeTest extends TestCase
{
    public function testGetHandlesWithoutHandlers(): void
    {
        $composite = new LayoutHandlerComposite($this->createMock(LayoutInterface::class));
        $this->assertSame(['default', 'foo'], $composite->getHandles(['default', 'foo', 'foo']));
    }

    public function testGetHandlesChainsHandlersAndSkipsInvalidOnes(): void
    {
        $composite = new LayoutHandlerComposite($this->createMock(LayoutInterface::class), [
            $this->getHandler(fn (array $handles) => [...$handles, 'added']),
            new stdClass(),
            $this->getHandler(fn (array $handles) => array_values(array_diff($handles, ['removed']))),
        ]);

        $this->assertSame(['default', 'added'], $composite->getHandles(['default', 'removed']));
    }

    public function testExecuteAddsAndRemovesHandlesOnLayout(): void
    {
        $added = [];
        $removed = [];

        $update = $this->createMock(ProcessorInterface::class);
        $update->method('addHandle')->willReturnCallback(function ($handle) use (&$added, $update) {
            $added[] = $handle;
            return $update;
        });
        $update->method('removeHandle')->willReturnCallback(function ($handle) use (&$removed, $update) {
            $removed[] = $handle;
            return $update;
        });

        $layout = $this->createMock(LayoutInterface::class);
        $layout->method('getUpdate')->willReturn($update);

        $composite = new LayoutHandlerComposite($layout, [
            $this->getHandler(fn (array $handles) => ['default', 'added']),
        ]);
        $composite->execute(['default', 'removed']);

        $this->assertSame(['default', 'added'], $added);
        $this->assertSame(['removed'], $removed);
    }

    private function getHandler(callable $callback): LayoutHandlerInterface
    {
        $handler = $this->createMock(LayoutHandlerInterface::class);
        $handler->method('get')->willReturnCallback($callback);

        return $handler;
    }
}
