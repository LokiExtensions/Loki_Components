<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Messages;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Message\Session as MessageSession;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class GlobalMessageRegistry implements ArgumentInterface
{
    /** @var GlobalMessage[] */
    protected array $messages = [];

    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly MessageSession $messageSession
    ) {
    }

    public function add(GlobalMessage $message): void
    {
        $this->messages[] = $message;
    }

    public function addSuccess(string $message): void
    {
        $this->add(new GlobalMessage($message, GlobalMessage::TYPE_SUCCESS));
    }

    public function addNotice(string $message): void
    {
        $this->add(new GlobalMessage($message, GlobalMessage::TYPE_NOTICE));
    }

    public function addWarning(string $message): void
    {
        $this->add(new GlobalMessage($message, GlobalMessage::TYPE_WARNING));
    }

    public function addError(string $message): void
    {
        $this->add(new GlobalMessage($message, GlobalMessage::TYPE_ERROR));
    }

    /**
     * @return GlobalMessage[]
     */
    public function getMessages(): array
    {
        $messageCollection = $this->messageManager->getMessages(true, 'default');
        foreach ($messageCollection->getItems() as $message) {
            $this->add(GlobalMessage::fromMessage($message));
        }

        return array_unique($this->messages, SORT_REGULAR);
    }

    public function toArray(): array
    {
        $messages = [];
        foreach ($this->getMessages() as $message) {
            $messages[] = [
                'text' => addslashes($message->getText()),
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
