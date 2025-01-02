<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Filter\Filter;
use Yireo\LokiComponents\Messages\GlobalMessageRegistry;
use Yireo\LokiComponents\Validator\Validator;
use Yireo\LokiComponents\Messages\LocalMessageRegistry;

abstract class ComponentRepository implements ComponentRepositoryInterface
{
    public function __construct(
        protected ComponentInterface $component,
        protected Validator $validator,
        protected Filter $filter,
    ) {
    }

    public function get(): mixed
    {
        $data = $this->filter($this->getData());
        $this->validate($data);
        return $data;
    }

    public function save(mixed $data): void
    {
        $data = $this->filter($data);

        if ($this->validate($data)) {
            $this->saveData($data);
        }
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

    abstract protected function getData(): mixed;

    abstract protected function saveData(mixed $data): void;

    protected function getContext(): ComponentContextInterface
    {
        return $this->component->getContext();
    }

    protected function filter(mixed $data): mixed
    {
        return $this->filter->filter($this->component->getFilters(), $data);
    }

    protected function validate(mixed $data): bool
    {
        return $this->validator->validate($this->component, $data);
    }
}
