<?php declare(strict_types=1);

namespace Loki\Components\Util\Block;

class TransferableAncestorBlockProperties
{
    private array $properties;

    public function __construct(
        array $properties = []
    ) {
        $this->properties = $properties;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
