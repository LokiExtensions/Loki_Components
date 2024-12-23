<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Messages;

class Message
{
    const TYPE_NOTICE = 'notice';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';

    public function __construct(
        private string $text,
        private string $type = self::TYPE_NOTICE
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
