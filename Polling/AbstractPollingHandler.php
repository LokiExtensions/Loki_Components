<?php
declare(strict_types=1);

namespace Loki\Components\Polling;

abstract class AbstractPollingHandler implements PollingHandlerInterface
{
    abstract public function execute(): array;

    public function getMessageData(string $messageType, string $message): array
    {
        return ['messageType' => $messageType, 'message' => $message];
    }
}
