<?php
declare(strict_types=1);

namespace Loki\Components\Messages;

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

    public function toSession(): void
    {
        foreach ($this->messages as $message) {
            if ($message->getType() === 'notice') {
                $this->messageManager->addNoticeMessage($message->getText());
            }

            if ($message->getType() === 'success') {
                $this->messageManager->addSuccessMessage($message->getText());
            }

            if ($message->getType() === 'warning') {
                $this->messageManager->addWarningMessage($message->getText());
            }

            if ($message->getType() === 'error') {
                $this->messageManager->addErrorMessage($message->getText());
            }
        }
    }

    public function toArray(): array
    {
        $messages = [];
        foreach ($this->getMessages() as $message) {
            $messages[] = [
                'text' => urlencode($message->getText()),
                'type' => $message->getType(),
            ];
        }

        return $messages;
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
