<?php declare(strict_types=1);

namespace Loki\Components\Filter;

class PositiveNumber implements FilterInterface
{
    public function filter(mixed $value, FilterScope $scope): mixed
    {
        if ($value > 0) {
            return (int)$value;
        }

        return 0;
    }
}
