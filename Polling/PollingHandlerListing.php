<?php
declare(strict_types=1);

namespace Loki\Components\Polling;

class PollingHandlerListing
{
    public function __construct(
        private readonly array $handlers,
    ) {
    }

    /**
     * @return PollingHandlerInterface[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}
