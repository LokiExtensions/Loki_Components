<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Messages;

use Loki\Components\Messages\GlobalMessage;
use Magento\Framework\Message\MessageInterface;
use PHPUnit\Framework\TestCase;

class GlobalMessageTest extends TestCase
{
    public function testDefaultTypeIsNotice(): void
    {
        $message = new GlobalMessage('Hello');
        $this->assertSame('Hello', $message->getText());
        $this->assertSame(GlobalMessage::TYPE_NOTICE, $message->getType());
    }

    public function testFromMessage(): void
    {
        $magentoMessage = $this->createMock(MessageInterface::class);
        $magentoMessage->method('getText')->willReturn('Saved');
        $magentoMessage->method('getType')->willReturn(MessageInterface::TYPE_SUCCESS);

        $message = GlobalMessage::fromMessage($magentoMessage);
        $this->assertSame('Saved', $message->getText());
        $this->assertSame(GlobalMessage::TYPE_SUCCESS, $message->getType());
    }
}
