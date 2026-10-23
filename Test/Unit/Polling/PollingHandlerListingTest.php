<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Polling;

use Loki\Components\Polling\PollingHandlerInterface;
use Loki\Components\Polling\PollingHandlerListing;
use PHPUnit\Framework\TestCase;

class PollingHandlerListingTest extends TestCase
{
    public function testEmptyByDefault(): void
    {
        $this->assertSame([], (new PollingHandlerListing())->getHandlers());
    }

    public function testReturnsInjectedHandlers(): void
    {
        $handlers = [
            'foo' => $this->createMock(PollingHandlerInterface::class),
            'bar' => $this->createMock(PollingHandlerInterface::class),
        ];

        $this->assertSame($handlers, (new PollingHandlerListing($handlers))->getHandlers());
    }
}
