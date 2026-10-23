<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Polling;

use Loki\Components\Polling\AbstractPollingHandler;
use Loki\Components\Polling\PollingHandlerInterface;
use PHPUnit\Framework\TestCase;

class AbstractPollingHandlerTest extends TestCase
{
    public function testGetMessageData(): void
    {
        $handler = new class extends AbstractPollingHandler {
            public function execute(): array
            {
                return [$this->getMessageData('notice', 'Hello world')];
            }
        };

        $this->assertInstanceOf(PollingHandlerInterface::class, $handler);
        $this->assertSame(
            ['messageType' => 'error', 'message' => 'Something failed'],
            $handler->getMessageData('error', 'Something failed')
        );
        $this->assertSame([['messageType' => 'notice', 'message' => 'Hello world']], $handler->execute());
    }
}
