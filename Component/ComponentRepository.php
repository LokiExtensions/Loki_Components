<?php
declare(strict_types=1);

namespace Loki\Components\Component;

use Loki\Components\Messages\GlobalMessageRegistry;
use Loki\Components\Messages\LocalMessageRegistry;

// @todo: Move this into a trait, so that repository don't extend anymore?
abstract class ComponentRepository implements ComponentRepositoryInterface
{
    protected ?ComponentInterface $component = null;

    public function setComponent(ComponentInterface $component): void
    {
        $this->component = $component;
    }

    public function getComponent(): ComponentInterface
    {
        return $this->component;
    }

    public function getComponentName(): string
    {
        return $this->component->getName();
    }

    public function getGlobalMessageRegistry(): GlobalMessageRegistry
    {
        return $this->component->getGlobalMessageRegistry();
    }

    public function getLocalMessageRegistry(): LocalMessageRegistry
    {
        return $this->component->getLocalMessageRegistry();
    }

    abstract public function getValue(): mixed;

    public function getDefaultValue(): mixed
    {
        return null;
    }

    abstract public function saveValue(mixed $value): void;

    protected function getContext(): ComponentContextInterface
    {
        return $this->component->getContext();
    }

    public function getPriority(): int
    {
        return 0;
    }
}
