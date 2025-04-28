<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Messages;

use Yireo\LokiComponents\Component\ComponentInterface;

class LocalMessageRegistry
{
    /** @var LocalMessage[] */
    private array $messages = [];

    public function add(LocalMessage $message): void
    {
        $this->messages[] = $message;
    }

    public function addSuccess(string $message): void
    {
        $this->messages[] = new LocalMessage($message, LocalMessage::TYPE_SUCCESS);
    }

    public function addNotice(string $message): void
    {
        $this->messages[] = new LocalMessage($message, LocalMessage::TYPE_NOTICE);
    }

    public function addWarning(string $message): void
    {
        $this->messages[] = new LocalMessage($message, LocalMessage::TYPE_WARNING);
    }

    public function addError(string $message): void
    {
        $this->messages[] = new LocalMessage($message, LocalMessage::TYPE_ERROR);
    }

    /**
     * @return LocalMessage[]
     */
    public function getMessages(): array
    {
        return array_unique($this->messages, SORT_REGULAR);
    }

    /**
     * @param ComponentInterface $component
     * @return array
     * @deprecated Use getMessages() instead
     */
    public function getMessagesByComponent(ComponentInterface $component): array
    {
        return $component->getLocalMessageRegistry()->getMessages();
    }

    public function hasMessages(): bool
    {
        return $this->messages !== [];
    }

    public function clearMessages(): void
    {
        $this->messages = [];
    }
}
