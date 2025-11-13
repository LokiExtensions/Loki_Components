<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class Capitalize implements FilterInterface
{
    public function filter(mixed $value, FilterScope $scope): mixed
    {
        return ucfirst((string)$value);
    }
}
