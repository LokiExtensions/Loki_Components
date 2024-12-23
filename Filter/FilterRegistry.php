<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

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
     * @param array $selectedFilters
     * @return FilterInterface[]
     */
    public function getSelectedFilters(array $selectedFilters = []): array
    {
        $filters = [];
        foreach ($selectedFilters as $filter) {
            if (array_key_exists($filter, $this->filters)) {
                $filters[$filter] = $this->filters[$filter];
            }
        }

        return $filters;
    }
}
