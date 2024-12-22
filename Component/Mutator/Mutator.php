<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component\Mutator;

use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Component\MutableComponentInterface;

abstract class Mutator implements MutatorInterface
{
    protected ?MutableComponentInterface $component = null;

    public function __construct(
        private Context $context
    ) {
    }

    public function getComponent(): MutableComponentInterface
    {
        return $this->component;
    }

    public function setComponent(MutableComponentInterface $component): void
    {
        $this->component = $component;
    }

    public function hasComponent(): bool
    {
        return $this->component instanceof MutableComponentInterface;
    }

    public function mutate(mixed $data): void
    {
        $filters = $this->context->getFilterRegistry()->getSelectedFilters(
            $this->getComponent()->getFilters()
        );

        foreach ($filters as $filter) {
            $data = $filter->filter($data);
        }

        $validators = $this->context->getValidatorRegistry()->getSelectedValidators(
            $this->getComponent()->getValidators()
        );

        foreach ($validators as $validator) {
            if ($validator->validate($this->getComponent())) {
                return; // @todo: Fail validation
            }
        }

        $this->save($data);
    }

    abstract function save($data): void;
}
