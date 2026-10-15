<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Messages;

use Loki\Components\Messages\LocalMessage;
use PHPUnit\Framework\TestCase;

class LocalMessageTest extends TestCase
{
    public function testDefaultTypeIsNotice(): void
    {
        $message = new LocalMessage('Hello');
        $this->assertSame('Hello', $message->getText());
        $this->assertSame(LocalMessage::TYPE_NOTICE, $message->getType());
    }

    public function testSerialization(): void
    {
        $message = new LocalMessage('Something failed', LocalMessage::TYPE_ERROR);
        $expected = ['text' => 'Something failed', 'type' => 'error'];

        $this->assertSame($expected, $message->toArray());
        $this->assertSame($expected, $message->jsonSerialize());
        $this->assertSame('{"text":"Something failed","type":"error"}', json_encode($message));
    }
}
