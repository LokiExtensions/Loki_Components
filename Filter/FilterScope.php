<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use Loki\Components\Component\ComponentInterface;

class FilterScope
{
    private ComponentInterface|null $component = null;
    private mixed $property = null;

    public function getComponent(): ?ComponentInterface
    {
        return $this->component;
    }

    public function setComponent(?ComponentInterface $component): void
    {
        $this->component = $component;
    }

    public function getProperty(): mixed
    {
        return $this->property;
    }

    public function setProperty(mixed $property): void
    {
        $this->property = $property;
    }
}
