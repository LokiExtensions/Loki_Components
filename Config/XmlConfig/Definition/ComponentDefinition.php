<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig\Definition;

use Yireo\LokiComponents\Component\ComponentInterface;

class ComponentDefinition
{
    public function __construct(
        private string $name,
        private string $domId,
        private ?ComponentInterface $component,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDomId(): string
    {
        return $this->domId;
    }

    public function getComponent(): ?ComponentInterface
    {
        return $this->component;
    }
}
