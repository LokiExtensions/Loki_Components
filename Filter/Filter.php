<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use Loki\Components\Component\ComponentInterface;

class Filter
{
    public function __construct(
        private readonly FilterRegistry $filterRegistry
    ) {
    }

    public function filter(
        ComponentInterface $component,
        mixed $data = null,
        ?string $scope = null
    ): mixed {
        $requestedFilters = $component->getFilters();

        if (empty($data) || is_bool($data) || is_int($data)) {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $data[$name] = $this->filter($component, $value, $name);
            }

            return $data;
        }

        $filters = $this->filterRegistry->getApplicableFilters(
            $requestedFilters
        );
        foreach ($filters as $filter) {
            $data = $filter->filter($data, $component, $scope);
        }

        return $data;
    }
}
