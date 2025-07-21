<?php declare(strict_types=1);

namespace Loki\Components\Util;

class DefaultTargets
{
    public function __construct(
        private readonly array $targets = []
    ) {
    }

    public function getTargets(): array
    {
        return $this->targets;
    }
}
