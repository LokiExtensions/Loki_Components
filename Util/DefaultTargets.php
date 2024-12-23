<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Util;

class DefaultTargets
{
    public function __construct(
        private array $targets = []
    ) {
    }

    public function getTargets(): array
    {
        $debugging = true; // @todo: Make this configurable
        if ($debugging) {
            $this->targets[] = 'loki-components.debugger';
        }

        return $this->targets;
    }
}
