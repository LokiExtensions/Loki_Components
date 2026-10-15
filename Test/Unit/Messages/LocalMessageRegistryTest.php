<?php declare(strict_types=1);

namespace Loki\Components\Test\Unit\Messages;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Messages\LocalMessage;
use Loki\Components\Messages\LocalMessageRegistry;
use PHPUnit\Framework\TestCase;

class LocalMessageRegistryTest extends TestCase
{
    public function testAddMessagesOfEachType(): void
    {
        $registry = new LocalMessageRegistry();
        $this->assertFalse($registry->hasMessages());

        $registry->addSuccess('Success');
        $registry->addNotice('Notice');
        $registry->addWarning('Warning');
        $registry->addError('Error');
        $registry->add(new LocalMessage('Custom'));

        $this->assertTrue($registry->hasMessages());
        $this->assertSame(
            [
                ['text' => 'Success', 'type' => LocalMessage::TYPE_SUCCESS],
                ['text' => 'Notice', 'type' => LocalMessage::TYPE_NOTICE],
                ['text' => 'Warning', 'type' => LocalMessage::TYPE_WARNING],
                ['text' => 'Error', 'type' => LocalMessage::TYPE_ERROR],
                ['text' => 'Custom', 'type' => LocalMessage::TYPE_NOTICE],
            ],
            array_map(fn (LocalMessage $message) => $message->toArray(), $registry->getMessages())
        );
    }

    public function testGetMessagesRemovesDuplicates(): void
    {
        $registry = new LocalMessageRegistry();
        $registry->addError('Error');
        $registry->addError('Error');
        $registry->addWarning('Error');

        $this->assertCount(2, $registry->getMessages());
    }

    public function testClearMessages(): void
    {
        $registry = new LocalMessageRegistry();
        $registry->addError('Error');
        $registry->clearMessages();

        $this->assertFalse($registry->hasMessages());
        $this->assertSame([], $registry->getMessages());
    }

    public function testGetMessagesByComponent(): void
    {
        $componentRegistry = new LocalMessageRegistry();
        $componentRegistry->addError('Component error');

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getLocalMessageRegistry')->willReturn($componentRegistry);

        $messages = (new LocalMessageRegistry())->getMessagesByComponent($component);
        $this->assertCount(1, $messages);
        $this->assertSame('Component error', $messages[0]->getText());
    }
}
