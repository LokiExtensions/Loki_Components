<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use Loki\Components\Component\ComponentInterface;

class Filter
{
    public function __construct(
        private readonly FilterRegistry $filterRegistry,
        private readonly FilterScopeFactory $filterScopeFactory,
    ) {
    }

    public function filter(
        ComponentInterface $component,
        mixed $data = null,
        mixed $propertyName = null
    ): mixed {
        $filters = $this->filterRegistry->getApplicableFilters(
            $component->getFilters()
        );

        $filterScope = $this->filterScopeFactory->create();
        $filterScope->setComponent($component);
        $filterScope->setProperty($propertyName);

        if (empty($data) || is_bool($data) || is_int($data)) {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $propertyName => $value) {
                $data[$propertyName] = $this->filter($component, $value, $propertyName);
            }

            return $data;
        }

        foreach ($filters as $filter) {
            $data = $filter->filter($data, $filterScope);
        }

        return $data;
    }
}
