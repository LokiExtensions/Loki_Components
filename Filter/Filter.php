<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Filter;

class Filter
{
    public function __construct(
        private FilterRegistry $filterRegistry
    ) {
    }

    public function filter(array $requestedFilters = [], mixed $data = null): mixed
    {
        if (empty($data) || is_bool($data) || is_int($data)) {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $data[$name] = $this->filter($requestedFilters, $value);
            }

            return $data;
        }

        $filters = $this->filterRegistry->getApplicableFilters($requestedFilters);
        foreach ($filters as $filter) {
            $data = $filter->filter($data);
        }

        return $data;
    }
}
