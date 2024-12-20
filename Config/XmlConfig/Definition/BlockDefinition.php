<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig\Definition;

class BlockDefinition
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getElementId(): string
    {
        return preg_replace('/([^a-z\-]+)/', '-', $this->name);
    }
}
