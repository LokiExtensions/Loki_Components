<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use InvalidArgumentException;

class FilterRegistry
{
    public function __construct(
        private array $filters = []
    ) {
        foreach ($this->filters as $filter) {
            if (false === $filter instanceof FilterInterface) {
                throw new InvalidArgumentException('Filter instance is not supported');
            }
        }
    }

    /**
     * @param array $filters
     * @return FilterInterface[]
     */
    public function getApplicableFilters(array $filters = []): array
    {
        $applicableFilters = [];
        foreach ($filters as $filter) {
            if (array_key_exists($filter, $this->filters)) {
                $applicableFilters[$filter] = $this->filters[$filter];
            }
        }

        return $applicableFilters;
    }
}
