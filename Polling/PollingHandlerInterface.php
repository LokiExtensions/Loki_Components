<?php
declare(strict_types=1);

namespace Loki\Components\Polling;

interface PollingHandlerInterface
{
    public function execute(): array;
}
