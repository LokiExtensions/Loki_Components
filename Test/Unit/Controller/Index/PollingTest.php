<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Controller\Index;

use Loki\Components\Controller\Index\Polling;
use Loki\Components\Polling\AbstractPollingHandler;
use Loki\Components\Polling\PollingHandlerInterface;
use Loki\Components\Polling\PollingHandlerListing;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use PHPUnit\Framework\TestCase;

class PollingTest extends TestCase
{
    public function testMergesDataOfAllPollingHandlers(): void
    {
        $messageHandler = new class extends AbstractPollingHandler {
            public function execute(): array
            {
                return ['stock' => $this->getMessageData('warning', 'Low stock')];
            }
        };

        $otherHandler = $this->createMock(PollingHandlerInterface::class);
        $otherHandler->method('execute')->willReturn(['other' => ['foo' => 'bar']]);

        $this->assertSame(
            [
                'stock' => ['messageType' => 'warning', 'message' => 'Low stock'],
                'other' => ['foo' => 'bar'],
            ],
            $this->executeController([$messageHandler, $otherHandler])
        );
    }

    public function testReturnsEmptyDataWithoutHandlers(): void
    {
        $this->assertSame([], $this->executeController([]));
    }

    public function testPollingHandlerListingHasNoRequiredHandlers(): void
    {
        $this->assertSame([], (new PollingHandlerListing())->getHandlers());
    }

    private function executeController(array $handlers): array
    {
        $data = null;
        $json = $this->createMock(Json::class);
        $json->method('setData')->willReturnCallback(function ($value) use (&$data, $json) {
            $data = $value;
            return $json;
        });

        $jsonFactory = $this->createMock(JsonFactory::class);
        $jsonFactory->method('create')->willReturn($json);

        $controller = new Polling($jsonFactory, new PollingHandlerListing($handlers));
        $this->assertSame($json, $controller->execute());

        return $data;
    }
}
