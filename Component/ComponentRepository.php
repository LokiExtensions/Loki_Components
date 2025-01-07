<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Filter\Filter;
use Yireo\LokiComponents\Messages\GlobalMessageRegistry;
use Yireo\LokiComponents\Validator\Validator;
use Yireo\LokiComponents\Messages\LocalMessageRegistry;

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

    abstract public function saveValue(mixed $data): void;

    protected function getContext(): ComponentContextInterface
    {
        return $this->component->getContext();
    }
}
