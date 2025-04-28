<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Messages;

use Magento\Framework\Message\MessageInterface;

class GlobalMessage
{
    const TYPE_NOTICE = 'notice';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';

    static public function fromMessage(MessageInterface $message): GlobalMessage
    {
        return new GlobalMessage($message->getText(), $message->getType());
    }
    public function __construct(
        private readonly string $text,
        private readonly string $type = self::TYPE_NOTICE
    ) {
    }

    public function getText(): string
    {
        return (string)__($this->text);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
