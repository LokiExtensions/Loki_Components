<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig\Definition;

class BlockDefinition
{
    public function __construct(
        private string $name,
        private string $elementId,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getElementId(): string
    {
        return $this->elementId;
    }
}
