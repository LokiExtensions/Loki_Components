<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Messages;

abstract class MessageManager
{
    /** @var Message[] */
    protected array $messages = [];

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
        return array_unique($this->messages, SORT_REGULAR);
    }

    public function toArray(): array
    {
        $messages = [];
        foreach ($this->getMessages() as $message) {
            $messages[] = [
                'text' => $message->getText(),
                'type' => $message->getType(),
            ];
        }

        return $messages;
    }

    public function hasMessages(): bool
    {
        return count($this->messages) > 0;
    }

    public function clearMessages(): void
    {
        $this->messages = [];
    }
}
