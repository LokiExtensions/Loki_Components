<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Component;

use Yireo\LokiComponents\Filter\FilterRegistry;
use Yireo\LokiComponents\Messages\MessageManager;
use Yireo\LokiComponents\Validator\ValidatorRegistry;

abstract class ComponentRepository implements ComponentRepositoryInterface
{
    public function __construct(
        protected ComponentInterface $component,
        protected ValidatorRegistry $validatorRegistry,
        protected FilterRegistry $filterRegistry,
        protected MessageManager $messageManager,
    ) {
    }

    public function get(): mixed
    {
        return $this->filter($this->getData());
    }

    public function save(mixed $data): void
    {
        $data = $this->filter($data);

        if ($this->validate($data)) {
            $this->saveData($data);
        }
    }

    abstract protected function getData(): mixed;

    abstract protected function saveData(mixed $data): void;

    protected function getContext(): ComponentContextInterface
    {
        return $this->component->getContext();
    }

    protected function filter(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $data[$name] = $this->filter($value);
            }

            return $data;
        }

        $filters = $this->filterRegistry->getSelectedFilters(
            $this->component->getFilters()
        );

        foreach ($filters as $filter) {
            $data = $filter->filter($data);
        }

        return $data;
    }

    protected function validate(mixed $data): bool
    {
        print_r($data);
        return true;
        if (is_array($data)) {
            foreach ($data as $value) {
                if ($this->validate($value)) {
                    return false;
                }
            }

            return true;
        }

        $validators = $this->validatorRegistry->getSelectedValidators(
            $this->component->getValidators()
        );

        foreach ($validators as $validator) {
            if (false === $validator->validate($this->component, $data)) {
                return false;
            }
        }

        return true;
    }
}
