<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Messages;

use Yireo\LokiComponents\Component\ComponentInterface;

class LocalMessageRegistry
{
    /** @var LocalMessage[] */
    protected array $messages = [];

    public function add(LocalMessage $message): void
    {
        $this->messages[] = $message;
    }

    public function addSuccess(ComponentInterface|string $component, string $message): void
    {
        if ($component instanceof ComponentInterface) {
            $component = $component->getName();
        }

        $this->messages[] = new LocalMessage($component, $message, LocalMessage::TYPE_SUCCESS);
    }

    public function addNotice(ComponentInterface|string $component, string $message): void
    {
        if ($component instanceof ComponentInterface) {
            $component = $component->getName();
        }
        $this->messages[] = new LocalMessage($component, $message, LocalMessage::TYPE_NOTICE);
    }

    public function addWarning(ComponentInterface|string $component, string $message): void
    {
        if ($component instanceof ComponentInterface) {
            $component = $component->getName();
        }
        $this->messages[] = new LocalMessage($component, $message, LocalMessage::TYPE_WARNING);
    }

    public function addError(ComponentInterface|string $component, string $message): void
    {
        if ($component instanceof ComponentInterface) {
            $component = $component->getName();
        }
        $this->messages[] = new LocalMessage($component, $message, LocalMessage::TYPE_ERROR);
    }

    /**
     * @return LocalMessage[]
     */
    public function getMessages(): array
    {
        return array_unique($this->messages, SORT_REGULAR);
    }

    public function getMessagesByComponent(ComponentInterface $component): array
    {
        return array_filter($this->getMessages(), function (LocalMessage $message) use ($component) {
            return $message->getComponentName() === $component->getName();
        });
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
