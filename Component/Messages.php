<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Component\Messages\Message;

class Messages
{
    /** @var Message[] */
    private array $messages = [];

    public function addNotice(string $message): void
    {
        $this->messages[] = new Message($message, Message::TYPE_NOTICE);
    }


    public function addWarning(string $message): void
    {
        $this->messages[] = new Message($message, Message::TYPE_WARNING);
    }

    public function addError(string $message): void
    {
        $this->messages[] = new Message($message, Message::TYPE_ERROR);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function hasMessages(): bool
    {
        return count($this->messages) > 0;
    }

    public function clear(): void
    {
        $this->messages = [];
    }
}
