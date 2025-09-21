<?php
declare(strict_types=1);

namespace Loki\Components\ViewModel;

use Loki\Components\Util\Ajax;
use Magento\Framework\Message\Manager as MessageManager;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Messages implements ArgumentInterface
{
    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly Ajax $ajax
    ) {
    }

    public function getMessagesAsJson(): string
    {
        if (false === $this->ajax->isAjax()) {
            return json_encode([]);
        }

        $messages = [];
        $messageCollection = $this->messageManager->getMessages(true);
        foreach ($messageCollection->getItems() as $message) {
            /** @var MessageInterface $message */
            $messages[] = [
                'type' => $message->getType(),
                'text' => $message->getText(),
            ];
        }

        return json_encode($messages);
    }
}
