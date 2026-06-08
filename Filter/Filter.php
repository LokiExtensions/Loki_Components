<?php declare(strict_types=1);

namespace Loki\Components\Filter;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Exception\RecursionException;

class Filter
{
    public function __construct(
        private readonly FilterRegistry $filterRegistry,
        private readonly FilterScopeFactory $filterScopeFactory,
        private readonly int $recursionDepth = 10
    ) {
    }

    public function filter(
        ComponentInterface $component,
        mixed $data = null,
        mixed $propertyName = null,
        int $depth = 0
    ): mixed {
        if ($depth >= $this->recursionDepth) {
            throw new RecursionException('Too many array levels');
        }

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
                $data[$propertyName] = $this->filter(
                    $component,
                    $value,
                    $propertyName,
                    $depth + 1
                );
            }

            return $data;
        }

        foreach ($filters as $filter) {
            $data = $filter->filter(
                $data,
                $filterScope
            );
        }

        return $data;
    }
}
