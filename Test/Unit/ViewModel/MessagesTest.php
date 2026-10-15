<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\ViewModel;

use Loki\Components\Util\Ajax;
use Loki\Components\ViewModel\Messages;
use Magento\Framework\Message\Collection;
use Magento\Framework\Message\Manager as MessageManager;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function testReturnsEmptyJsonOutsideAjaxRequests(): void
    {
        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->expects($this->never())->method('getMessages');

        $messages = new Messages(
            $messageManager,
            $this->createMock(InterpretationStrategyInterface::class),
            $this->getAjax(false)
        );

        $this->assertSame('[]', $messages->getMessagesAsJson());
    }

    public function testReturnsInterpretedMessagesDuringAjaxRequests(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->method('getType')->willReturn(MessageInterface::TYPE_ERROR);

        $collection = $this->createMock(Collection::class);
        $collection->method('getItems')->willReturn([$message]);

        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->expects($this->once())->method('getMessages')->with(true)->willReturn($collection);

        $interpretationStrategy = $this->createMock(InterpretationStrategyInterface::class);
        $interpretationStrategy->method('interpret')->with($message)->willReturn('Interpreted <b>error</b>');

        $messages = new Messages($messageManager, $interpretationStrategy, $this->getAjax(true));

        $this->assertSame(
            [['type' => 'error', 'text' => 'Interpreted <b>error</b>']],
            json_decode($messages->getMessagesAsJson(), true)
        );
    }

    public function testSyncedMessagesAreKeptUntilRendered(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->method('getType')->willReturn(MessageInterface::TYPE_SUCCESS);

        $firstCollection = $this->createMock(Collection::class);
        $firstCollection->method('getItems')->willReturn([$message]);
        $emptyCollection = $this->createMock(Collection::class);
        $emptyCollection->method('getItems')->willReturn([]);

        $messageManager = $this->createMock(MessageManager::class);
        $messageManager->method('getMessages')->willReturnOnConsecutiveCalls($firstCollection, $emptyCollection);

        $interpretationStrategy = $this->createMock(InterpretationStrategyInterface::class);
        $interpretationStrategy->method('interpret')->willReturn('Saved');

        $messages = new Messages($messageManager, $interpretationStrategy, $this->getAjax(true));
        $messages->syncMessages();

        $this->assertSame([['type' => 'success', 'text' => 'Saved']], json_decode($messages->getMessagesAsJson(), true));
    }

    private function getAjax(bool $isAjax): Ajax
    {
        $ajax = $this->createMock(Ajax::class);
        $ajax->method('isAjax')->willReturn($isAjax);

        return $ajax;
    }
}
